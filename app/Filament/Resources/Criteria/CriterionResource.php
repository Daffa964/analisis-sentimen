<?php

namespace App\Filament\Resources\Criteria;

use App\Filament\Resources\Criteria\Pages\CreateCriterion;
use App\Filament\Resources\Criteria\Pages\EditCriterion;
use App\Filament\Resources\Criteria\Pages\ListCriteria;
use App\Filament\Resources\Criteria\Schemas\CriterionForm;
use App\Filament\Resources\Criteria\Tables\CriteriaTable;
use App\Models\Criterion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CriterionResource extends Resource
{
    protected static ?string $model = Criterion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kriteria Pelayanan';
    protected static ?string $pluralModelLabel = 'Kriteria Pelayanan';
    protected static ?string $modelLabel = 'Kriteria';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CriterionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CriteriaTable::configure($table);
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
            'index' => ListCriteria::route('/'),
            'create' => CreateCriterion::route('/create'),
            'edit' => EditCriterion::route('/{record}/edit'),
        ];
    }
}
