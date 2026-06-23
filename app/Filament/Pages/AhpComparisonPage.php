<?php

namespace App\Filament\Pages;

use App\Models\Criterion;
use App\Models\AhpComparison;
use App\Services\AhpService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class AhpComparisonPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-scale';

    protected string $view = 'filament.pages.ahp-comparison-page';

    protected static ?string $title = 'Pembobotan AHP';

    protected static ?string $navigationLabel = 'Pembobotan AHP';

    protected static \UnitEnum|string|null $navigationGroup = 'Metode AHP-SAW';

    protected static ?int $navigationSort = 1;

    public array $criteria = [];
    
    // Matrix input state
    public array $matrixInput = [];
    
    // Calculation results state
    public ?array $results = null;
    public bool $isCalculated = false;

    public function mount(AhpService $ahpService)
    {
        $dbCriteria = Criterion::orderBy('code')->get();
        $this->criteria = $dbCriteria->toArray();

        // Load existing matrix from service
        $matrix = $ahpService->getComparisonMatrix();

        // Convert the matrix into user-friendly comparison inputs (C1_C2)
        foreach ($dbCriteria as $i => $c1) {
            foreach ($dbCriteria as $j => $c2) {
                if ($c1->id < $c2->id) {
                    $key = "{$c1->id}_{$c2->id}";
                    $val = $matrix[$c1->id][$c2->id] ?? 1.0;
                    
                    if ($val >= 1.0) {
                        $this->matrixInput[$key] = [
                            'dominant' => $c1->id,
                            'scale' => round($val),
                        ];
                    } else {
                        $this->matrixInput[$key] = [
                            'dominant' => $c2->id,
                            'scale' => round(1.0 / $val),
                        ];
                    }
                }
            }
        }

        // Run calculation initially if comparisons exist in the database
        if (AhpComparison::count() > 0) {
            $this->calculate($ahpService);
        }
    }

    public function calculate(AhpService $ahpService, bool $silent = false)
    {
        $matrix = [];
        
        // Initialize identity matrix
        foreach ($this->criteria as $c1) {
            foreach ($this->criteria as $c2) {
                $matrix[$c1['id']][$c2['id']] = ($c1['id'] == $c2['id']) ? 1.0 : null;
            }
        }

        // Fill with user comparisons
        foreach ($this->matrixInput as $key => $data) {
            list($c1Id, $c2Id) = explode('_', $key);
            $c1Id = (int)$c1Id;
            $c2Id = (int)$c2Id;
            $dominant = (int)$data['dominant'];
            $scale = (double)$data['scale'];

            if ($dominant === $c1Id) {
                $matrix[$c1Id][$c2Id] = $scale;
                $matrix[$c2Id][$c1Id] = 1.0 / $scale;
            } else {
                $matrix[$c1Id][$c2Id] = 1.0 / $scale;
                $matrix[$c2Id][$c1Id] = $scale;
            }
        }

        // Ensure diagonals are 1.0
        foreach ($this->criteria as $c) {
            $matrix[$c['id']][$c['id']] = 1.0;
        }

        // Call AhpService
        $this->results = $ahpService->calculate($matrix);
        $this->isCalculated = true;
        
        // Add criteria codes/names to weights for easy rendering
        $weightedCriteria = [];
        foreach ($this->criteria as $c) {
            $weightedCriteria[$c['id']] = [
                'code' => $c['code'],
                'name' => $c['name'],
                'weight' => $this->results['weights'][$c['id']] ?? 0.0
            ];
        }
        $this->results['weighted_criteria'] = $weightedCriteria;
        
        // Matrix in raw structure for display
        $this->results['matrix'] = $matrix;

        if (!$silent) {
            if ($this->results['is_consistent']) {
                Notification::make()
                    ->title('Kalkulasi AHP Berhasil!')
                    ->body('Matriks perbandingan berpasangan konsisten (CR < 0.1).')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Matriks Tidak Konsisten!')
                    ->body('Consistency Ratio (CR) >= 0.1. Silakan sesuaikan kembali nilai perbandingan.')
                    ->danger()
                    ->persistent()
                    ->send();
            }
        }
    }

    public function setComparisonValue(string $key, int $dominantId, int $scale)
    {
        $this->matrixInput[$key] = [
            'dominant' => $dominantId,
            'scale' => $scale,
        ];
        
        $this->calculate(app(AhpService::class), true);
    }

    public function saveWeights(AhpService $ahpService)
    {
        if (!$this->results || !$this->results['is_consistent']) {
            Notification::make()
                ->title('Gagal Menyimpan')
                ->body('Hasil kalkulasi tidak konsisten atau belum dihitung.')
                ->danger()
                ->send();
            return;
        }

        $success = $ahpService->saveComparisonsAndWeights($this->results['matrix'], $this->results);

        if ($success) {
            Notification::make()
                ->title('Bobot Kriteria Disimpan!')
                ->body('Perbandingan berpasangan dan bobot kriteria berhasil disimpan ke database.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal Menyimpan')
                ->body('Terjadi kesalahan saat menyimpan ke database.')
                ->danger()
                ->send();
        }
    }
}
