<?php

namespace App\Filament\Widgets;

use App\Models\SurveyPeriod;
use App\Services\SawService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SawRankingsChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'sawRankingsChart';

    /**
     * Widget Title
     *
     * @var string
     */
    protected static ?string $title = 'Grafik Kualitas Pelayanan Bangsal (SAW)';

    /**
     * Widget Sort
     */
    protected static ?int $sort = 2;

    /**
     * Columns span
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Filter options dropdown
     */
    protected function getFilters(): ?array
    {
        return [
            'regular' => 'Kategori Regular (Non-VIP)',
            'vip' => 'Kategori VIP / VVIP',
        ];
    }

    /**
     * Chart options (Apex Charts)
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $activePeriod = SurveyPeriod::where('is_active', true)->first();
        $category = $this->filter ?? 'regular';

        if (!$activePeriod) {
            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 300,
                ],
                'title' => [
                    'text' => 'Tidak ada periode survei aktif',
                    'align' => 'center',
                ],
                'series' => [],
            ];
        }

        $sawService = app(SawService::class);
        $sawResults = $sawService->calculate($activePeriod->id, $category);

        if (!$sawResults['has_data'] || empty($sawResults['rankings'])) {
            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 300,
                ],
                'title' => [
                    'text' => "Belum ada data penilaian untuk kategori " . strtoupper($category),
                    'align' => 'center',
                ],
                'series' => [],
            ];
        }

        $categories = [];
        $data = [];

        // Load rankings sorted descending by preference_value
        foreach ($sawResults['rankings'] as $rank) {
            $categories[] = $rank['ward_name'];
            $data[] = round($rank['preference_value'], 4);
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 320,
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '45%',
                    'borderRadius' => 6,
                    'distributed' => true, // Unique color for each bar
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '11px',
                    'fontWeight' => 'bold',
                ],
            ],
            'series' => [
                [
                    'name' => 'Nilai Preferensi (V)',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
                'labels' => [
                    'style' => [
                        'fontSize' => '11px',
                        'fontWeight' => 'semibold',
                    ],
                ],
            ],
            'yaxis' => [
                'min' => 0,
                'max' => 1,
                'title' => [
                    'text' => 'Nilai Preferensi',
                    'style' => [
                        'fontWeight' => 'bold',
                    ],
                ],
            ],
            'legend' => [
                'show' => false,
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'colors' => [
                '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', 
                '#ec4899', '#14b8a6', '#f97316', '#64748b', '#06b6d4'
            ],
        ];
    }
}
