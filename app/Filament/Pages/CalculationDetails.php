<?php

namespace App\Filament\Pages;

use App\Models\SurveyPeriod;
use App\Models\Criterion;
use App\Services\SawService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class CalculationDetails extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calculator';

    protected string $view = 'filament.pages.calculation-details';

    protected static ?string $title = 'Detail Perhitungan & Hasil';

    protected static ?string $navigationLabel = 'Detail Perhitungan';

    protected static \UnitEnum|string|null $navigationGroup = 'Metode AHP-SAW';

    protected static ?int $navigationSort = 2;

    public ?int $selectedPeriodId = null;
    public string $selectedCategory = 'regular';
    
    public array $periods = [];
    public array $criteria = [];
    
    // Result container
    public ?array $sawResults = null;
    public bool $hasData = false;

    public function mount()
    {
        $this->periods = SurveyPeriod::orderByDesc('start_date')->get()->toArray();
        $this->criteria = Criterion::orderBy('code')->get()->toArray();

        // Select the active period by default
        $activePeriod = SurveyPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->selectedPeriodId = $activePeriod->id;
        } elseif (!empty($this->periods)) {
            $this->selectedPeriodId = $this->periods[0]['id'];
        }

        if ($this->selectedPeriodId) {
            $this->runCalculation();
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

    public function runCalculation()
    {
        if (!$this->selectedPeriodId) {
            $this->sawResults = null;
            $this->hasData = false;
            return;
        }

        $sawService = app(SawService::class);
        $this->sawResults = $sawService->calculate($this->selectedPeriodId, $this->selectedCategory);
        $this->hasData = $this->sawResults['has_data'] ?? false;
    }
}
