<?php

namespace App\Filament\Widgets;

use App\Models\Ward;
use App\Models\Respondent;
use App\Models\SurveyPeriod;
use App\Models\AhpComparison;
use App\Services\AhpService;
use App\Services\SawService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Wards
        $totalWards = Ward::count();

        // 2. Active Period Respondents
        $activePeriod = SurveyPeriod::where('is_active', true)->first();
        $respondentsCount = 0;
        if ($activePeriod) {
            $respondentsCount = Respondent::where('survey_period_id', $activePeriod->id)->count();
        }

        // 3. AHP CR Status
        $crStatValue = 'Belum dihitung';
        $crDescription = 'Belum ada data bobot kriteria';
        $crColor = 'gray';
        $crIcon = 'heroicon-m-minus-circle';

        if (AhpComparison::count() > 0) {
            $ahpService = app(AhpService::class);
            $matrix = $ahpService->getComparisonMatrix();
            $ahpCalc = $ahpService->calculate($matrix);
            
            $crValue = number_format($ahpCalc['cr'], 4);
            if ($ahpCalc['is_consistent']) {
                $crStatValue = "KONSISTEN (CR: {$crValue})";
                $crDescription = 'Matriks perbandingan berpasangan konsisten';
                $crColor = 'success';
                $crIcon = 'heroicon-m-check-circle';
            } else {
                $crStatValue = "TIDAK KONSISTEN (CR: {$crValue})";
                $crDescription = 'Matriks perbandingan tidak konsisten';
                $crColor = 'danger';
                $crIcon = 'heroicon-m-x-circle';
            }
        }

        // 4. Best Regular Ward
        $bestRegular = 'Belum ada data';
        $bestRegularScore = '';
        if ($activePeriod && $respondentsCount > 0) {
            $sawService = app(SawService::class);
            $regularResults = $sawService->calculate($activePeriod->id, 'regular');
            if ($regularResults['has_data'] && !empty($regularResults['rankings'])) {
                $best = $regularResults['rankings'][0];
                $bestRegular = $best['ward_name'];
                $bestRegularScore = 'Skor: ' . number_format($best['preference_value'], 4);
            }
        }

        // 5. Best VIP Ward
        $bestVip = 'Belum ada data';
        $bestVipScore = '';
        if ($activePeriod && $respondentsCount > 0) {
            $sawService = app(SawService::class);
            $vipResults = $sawService->calculate($activePeriod->id, 'vip');
            if ($vipResults['has_data'] && !empty($vipResults['rankings'])) {
                $best = $vipResults['rankings'][0];
                $bestVip = $best['ward_name'];
                $bestVipScore = 'Skor: ' . number_format($best['preference_value'], 4);
            }
        }

        return [
            Stat::make('Total Bangsal', $totalWards)
                ->description('Jumlah bangsal terdaftar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
            Stat::make('Jumlah Responden (Periode Aktif)', $respondentsCount)
                ->description($activePeriod ? "Periode: {$activePeriod->name}" : 'Tidak ada periode aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color($respondentsCount > 0 ? 'success' : 'warning'),
            Stat::make('Konsistensi Matriks AHP', $crStatValue)
                ->description($crDescription)
                ->descriptionIcon($crIcon)
                ->color($crColor),
            Stat::make('Bangsal Regular Terbaik', $bestRegular)
                ->description($bestRegularScore ?: 'Menunggu responden')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),
            Stat::make('Bangsal VIP Terbaik', $bestVip)
                ->description($bestVipScore ?: 'Menunggu responden')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
        ];
    }
}
