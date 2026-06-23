<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Criterion;
use App\Models\Question;
use App\Models\Ward;
use App\Models\SurveyPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin & Manajemen)
        User::updateOrCreate(
            ['email' => 'admin@rsud.com'],
            [
                'name' => 'Administrator RSUD',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manajemen@rsud.com'],
            [
                'name' => 'Manajemen RSUD',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Seed Criteria
        $criteria = [
            ['code' => 'C1', 'name' => 'Pelayanan Administrasi'],
            ['code' => 'C2', 'name' => 'Kecepatan Pelayanan'],
            ['code' => 'C3', 'name' => 'Keramahan Petugas'],
            ['code' => 'C4', 'name' => 'Fasilitas Pelayanan'],
            ['code' => 'C5', 'name' => 'Kebersihan Lingkungan'],
            ['code' => 'C6', 'name' => 'Kemudahan Informasi Layanan'],
        ];

        $criteriaModels = [];
        foreach ($criteria as $item) {
            $criteriaModels[$item['code']] = Criterion::updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name']]
            );
        }

        // 3. Seed Questions
        $questions = [
            [
                'criterion_code' => 'C1',
                'question_code' => 'Q1',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kemudahan dan kejelasan prosedur administrasi penerimaan awal hingga proses pemulangan pasien di bangsal ini?'
            ],
            [
                'criterion_code' => 'C1',
                'question_code' => 'Q2',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kecekatan dan efisiensi petugas dalam menyelesaikan berkas administrasi atau klaim (BPJS/Umum) selama masa perawatan?'
            ],
            [
                'criterion_code' => 'C2',
                'question_code' => 'Q3',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kecepatan dan ketanggapan perawat/dokter saat Anda membutuhkan tindakan medis di bangsal?'
            ],
            [
                'criterion_code' => 'C2',
                'question_code' => 'Q4',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap waktu tunggu petugas (respon penanganan) saat Anda memanggil bantuan/menekan tombol panggil?'
            ],
            [
                'criterion_code' => 'C3',
                'question_code' => 'Q5',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap sikap sopan, ramah, dan kepedulian oleh perawat serta dokter selama merawat Anda?'
            ],
            [
                'criterion_code' => 'C3',
                'question_code' => 'Q6',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kesigapan dan rasa empati petugas dalam memberikan bantuan emosional maupun medis yang Anda perlukan?'
            ],
            [
                'criterion_code' => 'C4',
                'question_code' => 'Q7',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kualitas dan kelayakan fasilitas utama di dalam kamar pasien (seperti tempat tidur, AC/kipas angin, TV, dan bel panggil)?'
            ],
            [
                'criterion_code' => 'C4',
                'question_code' => 'Q8',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kelengkapan fasilitas penunjang di area bangsal?'
            ],
            [
                'criterion_code' => 'C5',
                'question_code' => 'Q9',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap higienitas dan kebersihan berkala pada area sensitif, khususnya kamar mandi/toilet serta tempat tidur kamar pasien?'
            ],
            [
                'criterion_code' => 'C5',
                'question_code' => 'Q10',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kebersihan dan kerapian area koridor bangsal serta lingkungan sekitar ruang perawatan?'
            ],
            [
                'criterion_code' => 'C6',
                'question_code' => 'Q11',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kejelasan petunjuk arah (signage) serta papan pengumuman yang terpasang di area bangsal perawatan?'
            ],
            [
                'criterion_code' => 'C6',
                'question_code' => 'Q12',
                'question_text' => 'Bagaimana tingkat kepuasan Anda terhadap kemudahan dalam memahami penjelasan petugas mengenai aturan kunjungan, jadwal dokter, maupun perkembangan kondisi kesehatan pasien?'
            ]
        ];

        foreach ($questions as $q) {
            $criterion = $criteriaModels[$q['criterion_code']];
            Question::updateOrCreate(
                ['question_code' => $q['question_code']],
                [
                    'criterion_id' => $criterion->id,
                    'question_text' => $q['question_text']
                ]
            );
        }

        // 4. Seed Wards
        $wards = [
            // VIP/VVIP Category
            ['name' => 'Edelweiss 4 (VVIP)', 'category' => 'vip', 'description' => 'Gedung Edelweiss lantai 4 kelas VVIP'],
            ['name' => 'Edelweiss 3 (VIP)', 'category' => 'vip', 'description' => 'Gedung Edelweiss lantai 3 kelas VIP'],
            ['name' => 'Edelweiss 2 (VIP)', 'category' => 'vip', 'description' => 'Gedung Edelweiss lantai 2 kelas VIP'],
            ['name' => 'Anggrek 1 (VIP)', 'category' => 'vip', 'description' => 'Gedung Anggrek lantai 1 kelas VIP'],
            ['name' => 'Anggrek 2 (VIP)', 'category' => 'vip', 'description' => 'Gedung Anggrek lantai 2 kelas VIP'],
            
            // Regular Category
            ['name' => 'Cempaka 1', 'category' => 'regular', 'description' => 'Bangsal Cempaka ruang 1 kelas Kelas 1, 2, 3'],
            ['name' => 'Cempaka 2', 'category' => 'regular', 'description' => 'Bangsal Cempaka ruang 2 kelas Kelas 1, 2, 3'],
            ['name' => 'Cempaka 3', 'category' => 'regular', 'description' => 'Bangsal Cempaka ruang 3 kelas Kelas 1, 2, 3'],
            ['name' => 'Bougenville 2', 'category' => 'regular', 'description' => 'Gedung Bougenville lantai 2'],
            ['name' => 'Bougenville 3', 'category' => 'regular', 'description' => 'Gedung Bougenville lantai 3'],
            ['name' => 'Melati 1', 'category' => 'regular', 'description' => 'Gedung Melati ruang 1'],
            ['name' => 'Dahlia 2', 'category' => 'regular', 'description' => 'Gedung Dahlia ruang 2'],
            ['name' => 'Fresia 4', 'category' => 'regular', 'description' => 'Gedung Fresia lantai 4'],
            ['name' => 'Fresia 5', 'category' => 'regular', 'description' => 'Gedung Fresia lantai 5'],
        ];

        foreach ($wards as $w) {
            Ward::updateOrCreate(
                ['name' => $w['name']],
                [
                    'category' => $w['category'],
                    'description' => $w['description'],
                    'qr_token' => Str::random(16)
                ]
            );
        }

        // 5. Seed active Survey Period
        SurveyPeriod::updateOrCreate(
            ['name' => 'Juni 2026'],
            [
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-30',
                'is_active' => true
            ]
        );
    }
}
