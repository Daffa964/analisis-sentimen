<?php

namespace App\Filament\Widgets;

use App\Models\SurveyPeriod;
use App\Models\Ward;
use App\Services\SawService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SawTrendChart extends ApexChartWidget
{
    protected static ?string $chartId = 'sawTrendChart';

    protected static ?string $title = 'Tren Indeks Kepuasan Pelayanan Bangsal (Lintas Periode)';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getFilters(): ?array
    {
        return [
            'regular' => 'Kategori Regular (Non-VIP)',
            'vip' => 'Kategori VIP / VVIP',
        ];
    }

    protected function getOptions(): array
    {
        $category = $this->filter ?? 'regular';
        
        // 1. Get all periods sorted by date
        $periods = SurveyPeriod::orderBy('start_date', 'asc')->get();
        
        if ($periods->isEmpty()) {
            return [
                'chart' => [
                    'type' => 'line',
                    'height' => 300,
                ],
                'title' => [
                    'text' => 'Belum ada periode survei terdaftar',
                    'align' => 'center',
                ],
                'series' => [],
            ];
        }

        // 2. Fetch wards in the selected category
        $wards = Ward::where('category', $category)->get();
        
        $sawService = app(SawService::class);
        $periodNames = [];
        $wardData = []; // [ward_name => [p1_val, p2_val, ...]]

        // Initialize empty arrays for each ward
        foreach ($wards as $ward) {
            $wardData[$ward->name] = [];
        }

        // 3. Loop through periods and calculate preference values
        foreach ($periods as $period) {
            $periodNames[] = $period->name;
            $sawResults = $sawService->calculate($period->id, $category);
            
            // Map the calculated preference value to each ward
            $prefMap = [];
            if ($sawResults['has_data'] && !empty($sawResults['rankings'])) {
                foreach ($sawResults['rankings'] as $rank) {
                    $prefMap[$rank['ward_name']] = round($rank['preference_value'], 4);
                }
            }

            foreach ($wards as $ward) {
                // If no data exists for this period, store null or 0.0
                $wardData[$ward->name][] = $prefMap[$ward->name] ?? null;
            }
        }

        // 4. Construct series
        $series = [];
        foreach ($wardData as $wardName => $dataPoints) {
            // Only add series if the ward has at least one non-null value (to avoid clutter)
            if (array_filter($dataPoints) !== []) {
                $series[] = [
                    'name' => $wardName,
                    'data' => $dataPoints,
                ];
            }
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
                'zoom' => [
                    'enabled' => false,
                ],
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'markers' => [
                'size' => 4,
            ],
            'grid' => [
                'row' => [
                    'colors' => ['transparent', 'transparent'],
                    'opacity' => 0.5,
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $periodNames,
                'title' => [
                    'text' => 'Periode Survei',
                    'style' => [
                        'fontWeight' => 'bold',
                    ],
                ],
            ],
            'yaxis' => [
                'min' => 0,
                'max' => 1,
                'title' => [
                    'text' => 'Nilai Indeks Kepuasan (V)',
                    'style' => [
                        'fontWeight' => 'bold',
                    ],
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
            'colors' => [
                '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', 
                '#ec4899', '#14b8a6', '#f97316', '#64748b', '#06b6d4'
            ],
        ];
    }
}
