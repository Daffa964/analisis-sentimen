<?php

use Livewire\Component;
use App\Models\Ward;
use App\Models\SurveyPeriod;
use App\Models\Question;
use App\Models\Respondent;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $token;
    public $ward;
    public $period;
    public $questions;

    // Form fields
    public $respondent_name;
    public $gender;
    public $age_group;
    public $answers = [];

    // State
    public $hasError = false;
    public $errorMessage = '';
    public $isSubmitted = false;

    public function mount($token)
    {
        $this->token = $token;
        $this->ward = Ward::where('qr_token', $token)->first();
        if (!$this->ward) {
            $this->hasError = true;
            $this->errorMessage = 'Bangsal tidak ditemukan atau link survei tidak valid.';
            return;
        }

        $this->period = SurveyPeriod::where('is_active', true)->first();
        if (!$this->period) {
            $this->hasError = true;
            $this->errorMessage = 'Maaf, saat ini tidak ada periode survei kuesioner yang sedang aktif.';
            return;
        }

        $this->questions = Question::orderBy('question_code')->get();
        foreach ($this->questions as $q) {
            $this->answers[$q->id] = null;
        }
    }

    public function submit()
    {
        $rules = [
            'gender' => 'required|in:Laki-laki,Perempuan',
            'age_group' => 'required|in:17-25,26-45,46-60,>60',
        ];

        foreach ($this->questions as $q) {
            $rules["answers.{$q->id}"] = 'required|integer|min:1|max:5';
        }

        $messages = [
            'gender.required' => 'Jenis Kelamin wajib dipilih.',
            'age_group.required' => 'Kelompok Usia wajib dipilih.',
        ];
        foreach ($this->questions as $q) {
            $messages["answers.{$q->id}.required"] = "Penilaian untuk nomor {$q->question_code} wajib diisi.";
        }

        $this->validate($rules, $messages);

        DB::transaction(function () {
            $respondent = Respondent::create([
                'survey_period_id' => $this->period->id,
                'ward_id' => $this->ward->id,
                'respondent_name' => $this->respondent_name ?: 'Anonim',
                'gender' => $this->gender,
                'age_group' => $this->age_group,
            ]);

            foreach ($this->answers as $qId => $score) {
                SurveyAnswer::create([
                    'respondent_id' => $respondent->id,
                    'question_id' => $qId,
                    'score' => $score,
                ]);
            }
        });

        $this->isSubmitted = true;
    }
};
?>

<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-tr from-emerald-50 via-slate-50 to-teal-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors duration-300">
    <div class="max-w-3xl mx-auto">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 bg-emerald-500 text-white rounded-2xl shadow-xl shadow-emerald-500/20 mb-4 ring-8 ring-emerald-500/10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                RSUD dr. Loekmono Hadi Kudus
            </h1>
            <p class="mt-2 text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                Evaluasi Kepuasan Pelayanan Rawat Inap Antar-Bangsal
            </p>
        </div>

        @if($hasError)
            <!-- Error State Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-xl border border-red-100 dark:border-red-950/30 text-center space-y-4">
                <div class="inline-flex p-3 bg-red-100 dark:bg-red-950/20 text-red-600 dark:text-red-400 rounded-full mb-2">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Akses Survei Ditolak</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto text-sm sm:text-base">
                    {{ $errorMessage }}
                </p>
                <div class="pt-4">
                    <span class="text-xs text-slate-400">Silakan hubungi petugas administrasi rawat inap RSUD jika terdapat kesalahan.</span>
                </div>
            </div>

        @elseif($isSubmitted)
            <!-- Success / Thank you State Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 shadow-2xl border border-emerald-100 dark:border-emerald-950/30 text-center space-y-6 animate-fade-in-up">
                <div class="inline-flex p-4 bg-emerald-100 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 rounded-full mb-2 ring-8 ring-emerald-500/5">
                    <svg class="w-16 h-16 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">Terima Kasih!</h2>
                <div class="space-y-2 max-w-md mx-auto">
                    <p class="text-slate-600 dark:text-slate-300 font-medium">
                        Penilaian Anda berhasil kami simpan.
                    </p>
                    <p class="text-sm text-slate-400 dark:text-slate-400">
                        Setiap masukan dan penilaian yang Anda berikan sangat berarti bagi kami untuk terus meningkatkan kualitas pelayanan rawat inap di <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $ward->name }}</span>.
                    </p>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800/80">
                    <p class="text-xs text-slate-400">RSUD dr. Loekmono Hadi Kudus &copy; {{ date('Y') }}</p>
                </div>
            </div>

        @else
            <!-- Questionnaire Form -->
            <form wire:submit.prevent="submit" class="space-y-8">
                <!-- Info Banner -->
                <div class="bg-emerald-600 dark:bg-emerald-800 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
                    <!-- Decorative background pattern -->
                    <div class="absolute right-0 top-0 translate-x-12 -translate-y-12 opacity-10 pointer-events-none">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 200 200">
                            <path d="M44.5,-75.4C58.3,-67.2,70.5,-56.3,77.7,-42.6C84.9,-29,87.1,-12.5,85.2,3.3C83.2,19.2,77.2,34.4,68.2,46.9C59.2,59.3,47.3,69.1,33.5,75.4C19.7,81.8,4,84.7,-11.2,82.4C-26.3,80.1,-40.8,72.6,-53.4,63.1C-66.1,53.6,-76.8,42,-82.1,28.2C-87.5,14.4,-87.3,-1.6,-83.4,-16C-79.6,-30.5,-72,-43.3,-61.4,-52C-50.8,-60.7,-37.2,-65.4,-23.9,-72.1C-10.7,-78.9,2.2,-87.7,15.6,-86.7C29,-85.7,40.7,-83.6,44.5,-75.4Z" transform="translate(100, 100)" />
                        </svg>
                    </div>
                    
                    <div class="relative z-10 space-y-2">
                        <span class="px-3 py-1 bg-emerald-500 dark:bg-emerald-700 text-xs font-bold uppercase tracking-wider rounded-full shadow-sm">
                            KUESIONER AKTIF: {{ $period->name }}
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black">Bangsal: {{ $ward->name }}</h2>
                        <p class="text-xs sm:text-sm text-emerald-100 leading-relaxed">
                            Bantu kami mengevaluasi kualitas layanan rawat inap dengan mengisi kuesioner ini. Penilaian Anda sepenuhnya dirahasiakan dan hanya digunakan untuk analisis peningkatan kualitas layanan rumah sakit.
                        </p>
                    </div>
                </div>

                <!-- Demographics Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-lg border border-slate-100 dark:border-slate-800/80 space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Profil Responden
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name field -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap (Opsional)</label>
                            <input type="text" wire:model.defer="respondent_name" placeholder="Boleh dikosongkan (Anonim)" 
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition-all dark:text-white">
                        </div>

                        <!-- Gender field -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Jenis Kelamin</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center px-4 py-3 rounded-xl border {{ $gender === 'Laki-laki' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-semibold' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400' }} cursor-pointer transition-all hover:bg-slate-100 dark:hover:bg-slate-800/50 text-sm">
                                    <input type="radio" wire:model="gender" value="Laki-laki" class="sr-only">
                                    Laki-laki
                                </label>
                                <label class="flex items-center justify-center px-4 py-3 rounded-xl border {{ $gender === 'Perempuan' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-semibold' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400' }} cursor-pointer transition-all hover:bg-slate-100 dark:hover:bg-slate-800/50 text-sm">
                                    <input type="radio" wire:model="gender" value="Perempuan" class="sr-only">
                                    Perempuan
                                </label>
                            </div>
                            @error('gender') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Age group field -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Kelompok Usia</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['17-25' => '17-25 Thn', '26-45' => '26-45 Thn', '46-60' => '46-60 Thn', '>60' => '>60 Thn'] as $val => $label)
                                    <label class="flex items-center justify-center px-2 py-3 rounded-xl border {{ $age_group === $val ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 font-semibold' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400' }} cursor-pointer transition-all hover:bg-slate-100 dark:hover:bg-slate-800/50 text-xs sm:text-sm">
                                        <input type="radio" wire:model="age_group" value="{{ $val }}" class="sr-only">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @error('age_group') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Questions Card List -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2 px-1">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Pertanyaan Kuesioner Pelayanan
                    </h3>
                    
                    @foreach($questions as $q)
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-lg border border-slate-100 dark:border-slate-800/80 space-y-6 transition-all hover:shadow-xl">
                            <!-- Question Header -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-xs font-bold font-mono bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-full">
                                        {{ $q->question_code }}
                                    </span>
                                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">
                                        Aspek: {{ $q->criterion->name }}
                                    </span>
                                </div>
                                <p class="text-sm sm:text-base text-slate-700 dark:text-slate-200 leading-relaxed font-semibold">
                                    {{ $q->question_text }}
                                </p>
                            </div>

                            <!-- Rating options (Likert scale 1-5) -->
                            <div>
                                <div class="grid grid-cols-5 gap-2 sm:gap-3 max-w-lg mx-auto">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border cursor-pointer transition-all hover:bg-slate-50 dark:hover:bg-slate-800/50 
                                            {{ (isset($answers[$q->id]) && (int)$answers[$q->id] === $i)
                                                ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 ring-2 ring-emerald-500/20' 
                                                : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 text-slate-400 dark:text-slate-500' }}">
                                            <input type="radio" wire:model="answers.{{ $q->id }}" value="{{ $i }}" class="sr-only">
                                            
                                            <!-- Star Icon / Number -->
                                            <span class="text-base sm:text-lg font-black {{ (isset($answers[$q->id]) && (int)$answers[$q->id] === $i) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-600' }}">
                                                {{ $i }}
                                            </span>
                                            
                                            <span class="text-[9px] mt-1 text-center leading-none hidden sm:block">
                                                @if($i == 1) Sangat Buruk
                                                @elseif($i == 2) Buruk
                                                @elseif($i == 3) Cukup
                                                @elseif($i == 4) Baik
                                                @elseif($i == 5) Sangat Baik
                                                @endif
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                                @error('answers.' . $q->id) 
                                    <span class="text-xs text-red-500 mt-2 block text-center font-medium">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col items-center justify-center py-6">
                    <button type="submit" 
                            class="w-full sm:w-auto px-12 py-4 bg-emerald-600 hover:bg-emerald-500 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white font-bold text-lg rounded-2xl shadow-lg shadow-emerald-600/20 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Kirim Penilaian
                    </button>
                    <span class="text-xs text-slate-400 mt-3">Mohon periksa kembali semua jawaban sebelum menekan tombol kirim.</span>
                </div>
            </form>
        @endif
    </div>
</div>