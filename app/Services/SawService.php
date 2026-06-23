<?php

namespace App\Services;

use App\Models\Ward;
use App\Models\Criterion;
use App\Models\AhpWeight;
use App\Models\SurveyPeriod;
use App\Models\Respondent;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\DB;

class SawService
{
    /**
     * Calculate SAW Rankings for a given period and ward category ('regular' or 'vip').
     */
    public function calculate(int $periodId, string $category): array
    {
        $period = SurveyPeriod::findOrFail($periodId);
        $criteria = Criterion::orderBy('code')->get();
        
        // 1. Get wards in the selected category
        $wards = Ward::where('category', $category)->get();
        $wardIds = $wards->pluck('id')->toArray();

        // 2. Fetch AHP Weights
        $weights = [];
        $savedWeights = AhpWeight::all()->pluck('weight', 'criterion_id')->toArray();
        foreach ($criteria as $c) {
            $weights[$c->id] = $savedWeights[$c->id] ?? (1.0 / $criteria->count());
        }

        // 3. Build Decision Matrix (X)
        // x[ward_id][criterion_id] = average score of all respondents in that ward
        $rawMatrix = [];
        $activeWards = []; // Only include wards that have respondents

        foreach ($wards as $ward) {
            // Get respondents for this ward in the selected period
            $respondents = Respondent::where('survey_period_id', $periodId)
                ->where('ward_id', $ward->id)
                ->get();

            if ($respondents->isEmpty()) {
                // If no respondents, skip ward in calculations or set to 0.
                // We skip it from the ranking calculations to avoid division issues.
                continue;
            }

            $activeWards[$ward->id] = $ward;
            
            // For each criterion, calculate the average score of all questions in it
            foreach ($criteria as $c) {
                // Find all questions linked to this criterion
                $questionIds = $c->questions->pluck('id')->toArray();

                if (empty($questionIds)) {
                    $rawMatrix[$ward->id][$c->id] = 0.0;
                    continue;
                }

                // Average score of survey answers for these questions by respondents of this ward
                $avgScore = DB::table('survey_answers')
                    ->join('respondents', 'survey_answers.respondent_id', '=', 'respondents.id')
                    ->whereIn('survey_answers.question_id', $questionIds)
                    ->where('respondents.ward_id', $ward->id)
                    ->where('respondents.survey_period_id', $periodId)
                    ->avg('survey_answers.score');

                $rawMatrix[$ward->id][$c->id] = $avgScore ? (double)$avgScore : 0.0;
            }
        }

        if (empty($activeWards)) {
            return [
                'has_data' => false,
                'wards' => [],
                'raw_matrix' => [],
                'max_values' => [],
                'normalized_matrix' => [],
                'weights' => $weights,
                'rankings' => []
            ];
        }

        // 4. Find Max values for benefit criteria (all are benefit criteria)
        $maxValues = [];
        foreach ($criteria as $c) {
            $max = 0.0;
            foreach (array_keys($activeWards) as $wardId) {
                if ($rawMatrix[$wardId][$c->id] > $max) {
                    $max = $rawMatrix[$wardId][$c->id];
                }
            }
            $maxValues[$c->id] = $max ?: 1.0; // avoid division by zero
        }

        // 5. Normalize Matrix (R)
        $normalizedMatrix = [];
        foreach (array_keys($activeWards) as $wardId) {
            foreach ($criteria as $c) {
                $normalizedMatrix[$wardId][$c->id] = $rawMatrix[$wardId][$c->id] / $maxValues[$c->id];
            }
        }

        // 6. Calculate Preference Value (V) & Rankings
        $rankings = [];
        foreach ($activeWards as $wardId => $ward) {
            $preferenceValue = 0.0;
            foreach ($criteria as $c) {
                $preferenceValue += $weights[$c->id] * $normalizedMatrix[$wardId][$c->id];
            }
            
            $rankings[] = [
                'ward_id' => $wardId,
                'ward_name' => $ward->name,
                'preference_value' => $preferenceValue,
                'raw_scores' => $rawMatrix[$wardId],
            ];
        }

        // Sort rankings descending by preference_value
        usort($rankings, function ($a, $b) {
            return $b['preference_value'] <=> $a['preference_value'];
        });

        // Add ranking index
        foreach ($rankings as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        return [
            'has_data' => true,
            'wards' => $activeWards,
            'raw_matrix' => $rawMatrix,
            'max_values' => $maxValues,
            'normalized_matrix' => $normalizedMatrix,
            'weights' => $weights,
            'rankings' => $rankings
        ];
    }
}
