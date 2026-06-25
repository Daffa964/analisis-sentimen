<?php

namespace App\Filament\Resources\SurveyPeriods;

use App\Filament\Resources\SurveyPeriods\Pages\CreateSurveyPeriod;
use App\Filament\Resources\SurveyPeriods\Pages\EditSurveyPeriod;
use App\Filament\Resources\SurveyPeriods\Pages\ListSurveyPeriods;
use App\Filament\Resources\SurveyPeriods\Schemas\SurveyPeriodForm;
use App\Filament\Resources\SurveyPeriods\Tables\SurveyPeriodsTable;
use App\Models\SurveyPeriod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SurveyPeriodResource extends Resource
{
    protected static ?string $model = SurveyPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Periode Survei';
    protected static ?string $pluralModelLabel = 'Periode Survei';
    protected static ?string $modelLabel = 'Periode';
    protected static \UnitEnum|string|null $navigationGroup = 'Data Kuesioner';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SurveyPeriodForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SurveyPeriodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSurveyPeriods::route('/'),
            'create' => CreateSurveyPeriod::route('/create'),
            'edit' => EditSurveyPeriod::route('/{record}/edit'),
        ];
    }
}
