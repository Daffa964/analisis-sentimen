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
    public function calculate(int $periodId, string $category, ?array $customWeights = null): array
    {
        $period = SurveyPeriod::findOrFail($periodId);
        $criteria = Criterion::orderBy('code')->get();
        
        // 1. Get wards in the selected category
        $wards = Ward::where('category', $category)->get();
        $wardIds = $wards->pluck('id')->toArray();

        // 2. Fetch AHP Weights
        $weights = [];
        if ($customWeights !== null) {
            $weights = $customWeights;
        } else {
            $savedWeights = AhpWeight::all()->pluck('weight', 'criterion_id')->toArray();
            foreach ($criteria as $c) {
                $weights[$c->id] = $savedWeights[$c->id] ?? (1.0 / $criteria->count());
            }
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

        // 4. Set Max values for benefit criteria using theoretical maximum from config
        $maxValues = [];
        $theoreticalMax = (double)config('spk.max_theoretical_score', 5.0);
        foreach ($criteria as $c) {
            $maxValues[$c->id] = $theoreticalMax;
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

        $rankings = $this->generateRecommendationsForWards($rankings);

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

    /**
     * Generate automated recommendations for each ward based on criteria scores.
     */
    public function generateRecommendationsForWards(array $rankings): array
    {
        $criteria = \App\Models\Criterion::all()->keyBy('id');
        
        $recTexts = [
            'C1' => 'Evaluasi alur loket pendaftaran dan berkas klaim BPJS/Umum untuk mempersingkat waktu tunggu penerimaan dan kepulangan pasien.',
            'C2' => 'Optimalkan waktu respon penanganan medis dan respon tombol panggilan (nurse call button) di bangsal perawatan.',
            'C3' => 'Lakukan pelatihan berkala terkait komunikasi terapeutik, keramahan, dan sikap empati untuk perawat dan staf administrasi.',
            'C4' => 'Audit dan perbaiki fasilitas utama kamar pasien (tempat tidur, pendingin ruangan, TV, toilet) serta kelengkapan penunjang bangsal.',
            'C5' => 'Tingkatkan frekuensi kontrol kebersihan kamar mandi pasien dan koridor bangsal serta higienitas seprai/kasur.',
            'C6' => 'Lengkapi petunjuk arah (signage) di area bangsal dan perjelas media informasi mengenai aturan kunjungan serta jadwal dokter.'
        ];

        foreach ($rankings as &$item) {
            $rawScores = $item['raw_scores'] ?? [];
            $lowCriteria = [];
            
            foreach ($rawScores as $cId => $score) {
                $criterion = $criteria->get($cId);
                if ($criterion && $score < 3.8) {
                    $lowCriteria[] = [
                        'code' => $criterion->code,
                        'name' => $criterion->name,
                        'score' => $score,
                        'rec' => $recTexts[$criterion->code] ?? 'Perlu dilakukan evaluasi kualitas pelayanan pada aspek ini.'
                    ];
                }
            }

            // Sort low criteria by score ascending (worst first)
            usort($lowCriteria, function($a, $b) {
                return $a['score'] <=> $b['score'];
            });

            $item['low_criteria'] = $lowCriteria;
            
            if (empty($lowCriteria)) {
                $item['recommendation'] = 'Kualitas pelayanan secara umum sudah sangat baik. Pertahankan kinerja dan lakukan pemantauan berkala untuk menjaga kepuasan pasien.';
            } else {
                $recs = [];
                foreach (array_slice($lowCriteria, 0, 2) as $low) { // Max 2 recommendations
                    $recs[] = "Pada aspek **{$low['name']}** (skor " . round($low['score'], 2) . "/5.0): {$low['rec']}";
                }
                $item['recommendation'] = implode("\n\n", $recs);
            }
        }

        return $rankings;
    }
}
