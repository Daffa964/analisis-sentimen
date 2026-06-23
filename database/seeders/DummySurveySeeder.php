<?php

namespace Database\Seeders;

use App\Models\Criterion;
use App\Models\Question;
use App\Models\Ward;
use App\Models\SurveyPeriod;
use App\Models\Respondent;
use App\Models\SurveyAnswer;
use App\Services\AhpService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummySurveySeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure master data criteria and questions exist
        $this->call(DatabaseSeeder::class);

        $period = SurveyPeriod::where('is_active', true)->first();
        if (!$period) {
            return;
        }

        // 2. Insert consistent AHP comparisons (CR < 0.1)
        // Let's define a mock pairwise matrix:
        // C1 (Admin) vs: C2=1, C3=2, C4=3, C5=2, C6=2
        // C2 (Speed) vs: C3=2, C4=3, C5=2, C6=2
        // C3 (Friendliness) vs: C4=2, C5=1, C6=1
        // C4 (Facilities) vs: C5=1/2, C6=1/2
        // C5 (Cleanliness) vs: C6=1
        
        $criteria = Criterion::orderBy('code')->get();
        $matrix = [];
        
        // Setup identity matrix
        foreach ($criteria as $c1) {
            foreach ($criteria as $c2) {
                $matrix[$c1->id][$c2->id] = ($c1->id == $c2->id) ? 1.0 : null;
            }
        }

        // Define specific consistent comparisons
        // C1 vs C2..C6
        $matrix[$criteria[0]->id][$criteria[1]->id] = 1.0;
        $matrix[$criteria[0]->id][$criteria[2]->id] = 2.0;
        $matrix[$criteria[0]->id][$criteria[3]->id] = 3.0;
        $matrix[$criteria[0]->id][$criteria[4]->id] = 2.0;
        $matrix[$criteria[0]->id][$criteria[5]->id] = 2.0;

        // C2 vs C3..C6
        $matrix[$criteria[1]->id][$criteria[2]->id] = 2.0;
        $matrix[$criteria[1]->id][$criteria[3]->id] = 3.0;
        $matrix[$criteria[1]->id][$criteria[4]->id] = 2.0;
        $matrix[$criteria[1]->id][$criteria[5]->id] = 2.0;

        // C3 vs C4..C6
        $matrix[$criteria[2]->id][$criteria[3]->id] = 2.0;
        $matrix[$criteria[2]->id][$criteria[4]->id] = 1.0;
        $matrix[$criteria[2]->id][$criteria[5]->id] = 1.0;

        // C4 vs C5..C6
        $matrix[$criteria[3]->id][$criteria[4]->id] = 0.5;
        $matrix[$criteria[3]->id][$criteria[5]->id] = 0.5;

        // C5 vs C6
        $matrix[$criteria[4]->id][$criteria[5]->id] = 1.0;

        // Fill in reciprocals
        foreach ($criteria as $c1) {
            foreach ($criteria as $c2) {
                if ($matrix[$c1->id][$c2->id] === null && $matrix[$c2->id][$c1->id] !== null) {
                    $matrix[$c1->id][$c2->id] = 1.0 / $matrix[$c2->id][$c1->id];
                }
            }
        }

        // Calculate and save using AhpService
        $ahpService = app(AhpService::class);
        $results = $ahpService->calculate($matrix);
        
        $ahpService->saveComparisonsAndWeights($matrix, $results);

        // 3. Generate dummy respondents and answers
        $wards = Ward::all();
        $questions = Question::all();

        // Define rating profiles for wards to create realistic variations in SAW ranking
        // Profile determines probability of scores: 5=Sangat Puas, 4=Puas, etc.
        $profiles = [
            'Edelweiss 4 (VVIP)' => [5 => 0.7, 4 => 0.25, 3 => 0.05, 2 => 0, 1 => 0], // Outstanding
            'Edelweiss 3 (VIP)' => [5 => 0.5, 4 => 0.4, 3 => 0.1, 2 => 0, 1 => 0],   // Excellent
            'Edelweiss 2 (VIP)' => [5 => 0.4, 4 => 0.4, 3 => 0.15, 2 => 0.05, 1 => 0], // Very Good
            'Anggrek 1 (VIP)' => [5 => 0.2, 4 => 0.5, 3 => 0.2, 2 => 0.1, 1 => 0],     // Average VIP
            'Anggrek 2 (VIP)' => [5 => 0.15, 4 => 0.4, 3 => 0.3, 2 => 0.15, 1 => 0],   // Lower VIP
            
            'Cempaka 1' => [5 => 0.6, 4 => 0.3, 3 => 0.1, 2 => 0, 1 => 0],           // Excellent Regular
            'Cempaka 2' => [5 => 0.4, 4 => 0.4, 3 => 0.15, 2 => 0.05, 1 => 0],         // Very Good Regular
            'Cempaka 3' => [5 => 0.2, 4 => 0.5, 3 => 0.2, 2 => 0.1, 1 => 0],           // Average Regular
            'Bougenville 2' => [5 => 0.3, 4 => 0.4, 3 => 0.2, 2 => 0.1, 1 => 0],       // Good Regular
            'Bougenville 3' => [5 => 0.1, 4 => 0.3, 3 => 0.4, 2 => 0.15, 1 => 0.05],   // Below average
            'Melati 1' => [5 => 0.05, 4 => 0.2, 3 => 0.4, 2 => 0.25, 1 => 0.1],       // Poor
            'Dahlia 2' => [5 => 0.2, 4 => 0.4, 3 => 0.3, 2 => 0.1, 1 => 0],           // Good
            'Fresia 4' => [5 => 0.1, 4 => 0.3, 3 => 0.5, 2 => 0.1, 1 => 0],           // Average
            'Fresia 5' => [5 => 0.05, 4 => 0.15, 3 => 0.35, 2 => 0.3, 1 => 0.15],    // Poor
        ];

        $genders = ['Laki-laki', 'Perempuan'];
        $ageGroups = ['17-25', '26-45', '46-60', '>60'];
        $dummyFirstNames = ['Budi', 'Siti', 'Eko', 'Rini', 'Agus', 'Dewi', 'Hendra', 'Mega', 'Joko', 'Tri', 'Sri', 'Yanto', 'Indah', 'Bambang', 'Wati'];
        $dummyLastNames = ['Santoso', 'Lestari', 'Wibowo', 'Pratama', 'Kusuma', 'Haryanto', 'Wijaya', 'Nugroho', 'Astuti', 'Hidayat'];

        DB::transaction(function () use ($period, $wards, $questions, $profiles, $genders, $ageGroups, $dummyFirstNames, $dummyLastNames) {
            foreach ($wards as $ward) {
                // Generate 8 to 15 respondents per ward
                $numRespondents = rand(8, 15);
                $profile = $profiles[$ward->name] ?? [5 => 0.2, 4 => 0.4, 3 => 0.3, 2 => 0.1, 1 => 0];

                for ($r = 0; $r < $numRespondents; $r++) {
                    $name = $dummyFirstNames[array_rand($dummyFirstNames)] . ' ' . $dummyLastNames[array_rand($dummyLastNames)];
                    
                    $respondent = Respondent::create([
                        'survey_period_id' => $period->id,
                        'ward_id' => $ward->id,
                        'respondent_name' => $name,
                        'gender' => $genders[array_rand($genders)],
                        'age_group' => $ageGroups[array_rand($ageGroups)],
                    ]);

                    foreach ($questions as $q) {
                        // Pick rating based on profile probabilities
                        $score = $this->getRandomScoreFromProfile($profile);
                        
                        SurveyAnswer::create([
                            'respondent_id' => $respondent->id,
                            'question_id' => $q->id,
                            'score' => $score,
                        ]);
                    }
                }
            }
        });
    }

    private function getRandomScoreFromProfile($profile)
    {
        $randVal = rand(1, 100) / 100;
        $cumulative = 0;
        foreach ($profile as $score => $prob) {
            $cumulative += $prob;
            if ($randVal <= $cumulative) {
                return $score;
            }
        }
        return 3; // Default fallback
    }
}
