<?php

namespace App\Filament\Resources\Respondents\Pages;

use App\Filament\Resources\Respondents\RespondentResource;
use App\Models\SurveyPeriod;
use App\Models\Ward;
use App\Models\Question;
use App\Models\Respondent;
use App\Models\SurveyAnswer;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListRespondents extends ListRecords
{
    protected static string $resource = RespondentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import_csv')
                ->label('Import Google Form (CSV)')
                ->icon('heroicon-o-document-arrow-up')
                ->color('info')
                ->form([
                    Select::make('survey_period_id')
                        ->label('Periode Survei')
                        ->options(SurveyPeriod::all()->pluck('name', 'id'))
                        ->required()
                        ->default(fn () => SurveyPeriod::where('is_active', true)->first()?->id),
                    FileUpload::make('csv_file')
                        ->label('File CSV Google Form')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->required()
                ])
                ->action(function (array $data) {
                    $filePath = Storage::disk('local')->path($data['csv_file']);
                    
                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('File tidak ditemukan atau gagal diunggah.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Open file
                    $file = fopen($filePath, 'r');
                    $header = fgetcsv($file, 0, ','); // Google Form CSV uses comma separator
                    
                    // Strip BOM if present
                    if ($header) {
                        $header[0] = preg_replace('/[\x{FEFF}\x{FFFE}]/u', '', $header[0]);
                    }

                    if (!$header || count($header) < 5) {
                        Notification::make()
                            ->title('Format CSV tidak valid (kolom kurang dari 5).')
                            ->danger()
                            ->send();
                        fclose($file);
                        return;
                    }

                    // Define keyword indices
                    $nameColIndex = null;
                    $genderColIndex = null;
                    $ageColIndex = null;
                    $wardColIndex = null;
                    $questionColIndices = [];

                    // Identify columns
                    foreach ($header as $index => $colName) {
                        $colNameLower = strtolower(trim($colName));
                        
                        if ($nameColIndex === null && (str_contains($colNameLower, 'nama') || str_contains($colNameLower, 'name') || str_contains($colNameLower, 'lengkap'))) {
                            $nameColIndex = $index;
                        }
                        elseif ($genderColIndex === null && (str_contains($colNameLower, 'kelamin') || str_contains($colNameLower, 'gender') || str_contains($colNameLower, 'sex') || str_contains($colNameLower, 'pria') || str_contains($colNameLower, 'wanita') || str_contains($colNameLower, 'l/p'))) {
                            $genderColIndex = $index;
                        }
                        elseif ($ageColIndex === null && (str_contains($colNameLower, 'usia') || str_contains($colNameLower, 'umur') || str_contains($colNameLower, 'age') || str_contains($colNameLower, 'tahun'))) {
                            $ageColIndex = $index;
                        }
                        elseif ($wardColIndex === null && (str_contains($colNameLower, 'bangsal') || str_contains($colNameLower, 'ruang') || str_contains($colNameLower, 'ward') || str_contains($colNameLower, 'kamar'))) {
                            $wardColIndex = $index;
                        }
                    }

                    // Fallbacks based on typical spreadsheet structures
                    if ($nameColIndex === null) $nameColIndex = 1;
                    if ($genderColIndex === null) $genderColIndex = 2;
                    if ($ageColIndex === null) $ageColIndex = 3;
                    if ($wardColIndex === null) $wardColIndex = 4;

                    // Q1 to Q12 are located sequentially starting right after demographic data
                    $startIndex = max($nameColIndex, $genderColIndex, $ageColIndex, $wardColIndex) + 1;
                    for ($q = 1; $q <= 12; $q++) {
                        $questionColIndices[$q] = $startIndex + ($q - 1);
                    }

                    $importedCount = 0;
                    $failedCount = 0;

                    $wards = Ward::all();
                    $questions = Question::orderBy('question_code')->get();

                    while (($row = fgetcsv($file, 0, ',')) !== false) {
                        // Skip empty rows or header duplicate
                        if (count($row) < $startIndex || empty(array_filter($row))) {
                            continue;
                        }

                        $rawName = trim($row[$nameColIndex] ?? 'Responden Google Form');
                        $rawGender = trim($row[$genderColIndex] ?? 'Laki-laki');
                        $rawAge = trim($row[$ageColIndex] ?? '26-45');
                        $rawWard = trim($row[$wardColIndex] ?? '');

                        if (empty($rawWard)) {
                            $failedCount++;
                            continue;
                        }

                        // Match Ward Name
                        $matchedWard = null;
                        foreach ($wards as $ward) {
                            if (strcasecmp($ward->name, $rawWard) === 0) {
                                $matchedWard = $ward;
                                break;
                            }
                        }

                        if (!$matchedWard) {
                            foreach ($wards as $ward) {
                                if (str_contains(strtolower($rawWard), strtolower($ward->name)) || str_contains(strtolower($ward->name), strtolower($rawWard))) {
                                    $matchedWard = $ward;
                                    break;
                                }
                            }
                        }

                        if (!$matchedWard) {
                            $cleanRawWard = preg_replace('/[^a-zA-Z0-9]/', '', $rawWard);
                            foreach ($wards as $ward) {
                                $cleanWardName = preg_replace('/[^a-zA-Z0-9]/', '', $ward->name);
                                if (str_contains(strtolower($cleanRawWard), strtolower($cleanWardName)) || str_contains(strtolower($cleanWardName), strtolower($cleanRawWard))) {
                                    $matchedWard = $ward;
                                    break;
                                }
                            }
                        }

                        if (!$matchedWard) {
                            $failedCount++;
                            continue;
                        }

                        // Map Gender
                        $gender = 'Laki-laki';
                        if (preg_match('/(perempuan|wanita|p)/i', $rawGender)) {
                            $gender = 'Perempuan';
                        }

                        // Map Age Group
                        $ageGroup = '26-45';
                        if (str_contains($rawAge, '17') || str_contains($rawAge, '25') || str_contains($rawAge, 'muda')) {
                            $ageGroup = '17-25';
                        } elseif (str_contains($rawAge, '26') || str_contains($rawAge, '45')) {
                            $ageGroup = '26-45';
                        } elseif (str_contains($rawAge, '46') || str_contains($rawAge, '60')) {
                            $ageGroup = '46-60';
                        } elseif (str_contains($rawAge, '>') || str_contains($rawAge, '60') || str_contains($rawAge, 'tua')) {
                            $ageGroup = '>60';
                        }

                        // Map Likert scores
                        $scores = [];
                        for ($q = 1; $q <= 12; $q++) {
                            $colIdx = $questionColIndices[$q] ?? null;
                            $rawVal = isset($row[$colIdx]) ? trim($row[$colIdx]) : '';
                            
                            $score = 3; // Default Cukup
                            if (is_numeric($rawVal)) {
                                $score = (int)$rawVal;
                            } else {
                                $rawValLower = strtolower($rawVal);
                                if (str_contains($rawValLower, 'sangat puas') || str_contains($rawValLower, 'sangat baik') || str_contains($rawValLower, 'sangat setuju')) {
                                    $score = 5;
                                } elseif (str_contains($rawValLower, 'tidak puas') && str_contains($rawValLower, 'sangat')) {
                                    $score = 1;
                                } elseif (str_contains($rawValLower, 'tidak puas') || str_contains($rawValLower, 'kurang puas') || str_contains($rawValLower, 'tidak baik') || str_contains($rawValLower, 'kurang baik')) {
                                    $score = 2;
                                } elseif (str_contains($rawValLower, 'puas') || str_contains($rawValLower, 'baik') || str_contains($rawValLower, 'setuju')) {
                                    $score = 4;
                                } elseif (str_contains($rawValLower, 'cukup') || str_contains($rawValLower, 'netral') || str_contains($rawValLower, 'biasa')) {
                                    $score = 3;
                                }
                            }
                            $scores[$q] = max(1, min(5, $score));
                        }

                        // Store respondent and answers
                        DB::transaction(function () use ($data, $matchedWard, $rawName, $gender, $ageGroup, $questions, $scores, &$importedCount) {
                            $respondent = Respondent::create([
                                'survey_period_id' => $data['survey_period_id'],
                                'ward_id' => $matchedWard->id,
                                'respondent_name' => $rawName,
                                'gender' => $gender,
                                'age_group' => $ageGroup,
                            ]);

                            foreach ($questions as $idx => $q) {
                                SurveyAnswer::create([
                                    'respondent_id' => $respondent->id,
                                    'question_id' => $q->id,
                                    'score' => $scores[$idx + 1] ?? 3
                                ]);
                            }

                            $importedCount++;
                        });
                    }

                    fclose($file);

                    // Clean up temp file
                    if (Storage::disk('local')->exists($data['csv_file'])) {
                        Storage::disk('local')->delete($data['csv_file']);
                    }

                    if ($importedCount > 0) {
                        Notification::make()
                            ->title("Import Berhasil!")
                            ->body("Telah mengimpor {$importedCount} data responden baru.")
                            ->success()
                            ->send();
                    }

                    if ($failedCount > 0) {
                        Notification::make()
                            ->title("Ada data terlewati!")
                            ->body("Gagal mengimpor {$failedCount} baris (nama bangsal tidak terdaftar/kosong).")
                            ->warning()
                            ->send();
                    }
                })
        ];
    }
}
