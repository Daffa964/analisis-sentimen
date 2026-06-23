<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\AhpComparison;
use App\Models\AhpWeight;
use Illuminate\Support\Facades\DB;

class AhpService
{
    // Saaty's Random Consistency Index (RI) table
    // n = 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
    protected static array $RI = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49
    ];

    /**
     * Get the pairwise comparison matrix from the database.
     * If empty, initializes a default matrix of 1s (completely equal).
     */
    public function getComparisonMatrix(): array
    {
        $criteria = Criterion::orderBy('code')->get();
        $n = $criteria->count();
        $matrix = [];

        // Pre-fill with identity values
        foreach ($criteria as $c1) {
            foreach ($criteria as $c2) {
                $matrix[$c1->id][$c2->id] = ($c1->id == $c2->id) ? 1.0 : null;
            }
        }

        // Fetch saved comparisons
        $comparisons = AhpComparison::all();
        foreach ($comparisons as $comp) {
            if (isset($matrix[$comp->criterion_1_id][$comp->criterion_2_id])) {
                $matrix[$comp->criterion_1_id][$comp->criterion_2_id] = (double)$comp->value;
                // Enforce reciprocal value on transpose
                $matrix[$comp->criterion_2_id][$comp->criterion_1_id] = 1.0 / (double)$comp->value;
            }
        }

        // Ensure all diagonal and reciprocals are filled
        foreach ($criteria as $c1) {
            foreach ($criteria as $c2) {
                if ($matrix[$c1->id][$c2->id] === null) {
                    $matrix[$c1->id][$c2->id] = 1.0;
                    $matrix[$c2->id][$c1->id] = 1.0;
                }
            }
        }

        return $matrix;
    }

    /**
     * Calculate AHP Weights, Lambda Max, CI, CR, and consistency check.
     */
    public function calculate(array $matrix): array
    {
        $criterionIds = array_keys($matrix);
        $n = count($criterionIds);

        if ($n === 0) {
            return [
                'weights' => [],
                'lambda_max' => 0,
                'ci' => 0,
                'cr' => 0,
                'is_consistent' => true
            ];
        }

        // 1. Calculate column sums
        $colSums = [];
        foreach ($criterionIds as $colId) {
            $sum = 0.0;
            foreach ($criterionIds as $rowId) {
                $sum += $matrix[$rowId][$colId];
            }
            $colSums[$colId] = $sum;
        }

        // 2. Normalize matrix & calculate weights (row averages)
        $normalizedMatrix = [];
        $weights = [];
        foreach ($criterionIds as $rowId) {
            $rowSumNormalized = 0.0;
            foreach ($criterionIds as $colId) {
                $normalizedValue = $matrix[$rowId][$colId] / ($colSums[$colId] ?: 1.0);
                $normalizedMatrix[$rowId][$colId] = $normalizedValue;
                $rowSumNormalized += $normalizedValue;
            }
            $weights[$rowId] = $rowSumNormalized / $n;
        }

        // 3. Consistency Test (A * W = X)
        $X = [];
        foreach ($criterionIds as $rowId) {
            $sum = 0.0;
            foreach ($criterionIds as $colId) {
                $sum += $matrix[$rowId][$colId] * $weights[$colId];
            }
            $X[$rowId] = $sum;
        }

        // 4. Calculate Lambda Max
        $lambdaSum = 0.0;
        foreach ($criterionIds as $rowId) {
            $lambdaSum += $X[$rowId] / ($weights[$rowId] ?: 1.0);
        }
        $lambdaMax = $lambdaSum / $n;

        // 5. Calculate Consistency Index (CI)
        $ci = 0.0;
        if ($n > 1) {
            $ci = ($lambdaMax - $n) / ($n - 1);
        }

        // 6. Calculate Consistency Ratio (CR)
        $ri = self::$RI[$n] ?? 1.45;
        $cr = ($ri > 0) ? ($ci / $ri) : 0.0;

        $isConsistent = ($cr < 0.10);

        return [
            'weights' => $weights,
            'lambda_max' => $lambdaMax,
            'ci' => $ci,
            'cr' => $cr,
            'is_consistent' => $isConsistent,
            'col_sums' => $colSums,
            'normalized_matrix' => $normalizedMatrix
        ];
    }

    /**
     * Save the AHP comparisons and weights to database if consistent.
     */
    public function saveComparisonsAndWeights(array $matrix, array $results): bool
    {
        if (!$results['is_consistent']) {
            return false;
        }

        return DB::transaction(function () use ($matrix, $results) {
            // Save comparisons
            AhpComparison::query()->delete();
            foreach ($matrix as $c1Id => $row) {
                foreach ($row as $c2Id => $val) {
                    // Only save upper triangle to avoid duplicates, or save all
                    // We save all to make query easy, or upper only. Let's save c1Id < c2Id
                    if ($c1Id < $c2Id) {
                        AhpComparison::create([
                            'criterion_1_id' => $c1Id,
                            'criterion_2_id' => $c2Id,
                            'value' => $val,
                        ]);
                    }
                }
            }

            // Save weights
            AhpWeight::query()->delete();
            foreach ($results['weights'] as $criterionId => $weight) {
                AhpWeight::create([
                    'criterion_id' => $criterionId,
                    'weight' => $weight
                ]);
            }

            return true;
        });
    }
}
