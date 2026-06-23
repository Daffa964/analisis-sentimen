<div class="space-y-6 p-4">
    <!-- Respondent Info -->
    <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 text-sm">
        <div>
            <span class="text-gray-400 block mb-1">Nama Responden</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ $respondent->respondent_name ?: 'Anonim' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block mb-1">Bangsal</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ $respondent->ward->name }}</span>
        </div>
        <div>
            <span class="text-gray-400 block mb-1">Jenis Kelamin</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ $respondent->gender ?: '-' }}</span>
        </div>
        <div>
            <span class="text-gray-400 block mb-1">Kelompok Usia</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ $respondent->age_group ? $respondent->age_group . ' Tahun' : '-' }}</span>
        </div>
        <div class="col-span-2">
            <span class="text-gray-400 block mb-1">Periode Survei</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ $respondent->surveyPeriod->name }}</span>
        </div>
    </div>

    <!-- Answers List -->
    <div class="space-y-4">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Rincian Skor Penilaian (Skala Likert 1-5)</h4>
        
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto pr-2">
            @foreach($respondent->surveyAnswers->sortBy('question.question_code') as $answer)
                <div class="py-3 flex flex-col md:flex-row md:items-center justify-between gap-2">
                    <div class="space-y-1 max-w-xl">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-xs font-mono font-bold bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300 rounded">
                                {{ $answer->question->question_code }}
                            </span>
                            <span class="text-xs text-gray-400">
                                Kriteria: {{ $answer->question->criterion->code }} - {{ $answer->question->criterion->name }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $answer->question->question_text }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 self-start md:self-center">
                        <span class="text-xs text-gray-400">Skor:</span>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $answer->score ? 'text-amber-400 fill-current' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="px-2 py-0.5 text-sm font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded">
                            {{ $answer->score }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
