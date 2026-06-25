<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SurveyFlyerWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.survey-flyer-widget';

    protected int | string | array $columnSpan = 'full';
}
