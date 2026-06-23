<?php

namespace App\Filament\Resources\Respondents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RespondentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('survey_period_id')
                    ->relationship('surveyPeriod', 'name')
                    ->required(),
                Select::make('ward_id')
                    ->relationship('ward', 'name')
                    ->required(),
                TextInput::make('respondent_name'),
                Select::make('gender')
                    ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']),
                Select::make('age_group')
                    ->options([
                        '17-25' => '17-25 Tahun',
                        '26-45' => '26-45 Tahun',
                        '46-60' => '46-60 Tahun',
                        '>60' => '>60 Tahun',
                    ]),
            ]);
    }
}
