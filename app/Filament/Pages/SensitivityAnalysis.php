<?php

namespace App\Filament\Pages;

use App\Models\SurveyPeriod;
use App\Models\Criterion;
use App\Models\AhpWeight;
use App\Services\SawService;
use Filament\Pages\Page;

class SensitivityAnalysis extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected string $view = 'filament.pages.sensitivity-analysis';

    protected static ?string $title = 'Simulasi Analisis Sensitivitas';

    protected static ?string $navigationLabel = 'Analisis Sensitivitas';

    protected static \UnitEnum|string|null $navigationGroup = 'Metode AHP-SAW';

    protected static ?int $navigationSort = 3;

    public ?int $selectedPeriodId = null;
    public string $selectedCategory = 'regular';

    public array $periods = [];
    public array $criteria = [];

    // Weights container: [criterion_id => value]
    public array $sliderWeights = []; // Scale 0-100 for GUI
    public array $normalizedWeights = []; // Scale 0-1 (sums to 1.0)
    public array $originalWeights = []; // Scale 0-1 from DB

    // Rankings container
    public ?array $originalResults = null;
    public ?array $simulatedResults = null;
    public bool $hasData = false;

    public function mount()
    {
        $this->periods = SurveyPeriod::orderByDesc('start_date')->get()->toArray();
        $this->criteria = Criterion::orderBy('code')->get()->toArray();

        // Select active period by default
        $activePeriod = SurveyPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->selectedPeriodId = $activePeriod->id;
        } elseif (!empty($this->periods)) {
            $this->selectedPeriodId = $this->periods[0]['id'];
        }

        $this->loadOriginalWeights();
        $this->resetSliders();
        $this->runCalculation();
    }

    public function loadOriginalWeights()
    {
        $savedWeights = AhpWeight::all()->pluck('weight', 'criterion_id')->toArray();
        $this->originalWeights = [];
        foreach ($this->criteria as $c) {
            $this->originalWeights[$c['id']] = $savedWeights[$c['id']] ?? (1.0 / count($this->criteria));
        }
    }

    public function resetSliders()
    {
        $this->sliderWeights = [];
        foreach ($this->originalWeights as $cId => $weight) {
            // Scale to 0-100 for range inputs
            $this->sliderWeights[$cId] = round($weight * 100);
        }
        $this->normalizeSliders();
    }

    public function normalizeSliders()
    {
        $sum = array_sum($this->sliderWeights);
        if ($sum == 0) {
            // Avoid division by zero: set all equal
            $count = count($this->criteria);
            foreach ($this->sliderWeights as $cId => $w) {
                $this->normalizedWeights[$cId] = 1.0 / $count;
            }
        } else {
            foreach ($this->sliderWeights as $cId => $w) {
                $this->normalizedWeights[$cId] = $w / $sum;
            }
        }
    }

    public function updatedSelectedPeriodId()
    {
        $this->runCalculation();
    }

    public function updatedSelectedCategory()
    {
        $this->runCalculation();
    }

    public function updatedSliderWeights()
    {
        $this->normalizeSliders();
        $this->runCalculation();
    }

    public function runCalculation()
    {
        if (!$this->selectedPeriodId) {
            $this->originalResults = null;
            $this->simulatedResults = null;
            $this->hasData = false;
            return;
        }

        $sawService = app(SawService::class);
        
        // 1. Calculate with original DB weights
        $this->originalResults = $sawService->calculate($this->selectedPeriodId, $this->selectedCategory, $this->originalWeights);
        
        // 2. Calculate with simulated normalized weights
        $this->simulatedResults = $sawService->calculate($this->selectedPeriodId, $this->selectedCategory, $this->normalizedWeights);
        
        $this->hasData = ($this->originalResults['has_data'] ?? false) && ($this->simulatedResults['has_data'] ?? false);
    }
}
