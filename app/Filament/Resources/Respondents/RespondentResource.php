<?php

namespace App\Filament\Resources\Respondents;

use App\Filament\Resources\Respondents\Pages\CreateRespondent;
use App\Filament\Resources\Respondents\Pages\EditRespondent;
use App\Filament\Resources\Respondents\Pages\ListRespondents;
use App\Filament\Resources\Respondents\Schemas\RespondentForm;
use App\Filament\Resources\Respondents\Tables\RespondentsTable;
use App\Models\Respondent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RespondentResource extends Resource
{
    protected static ?string $model = Respondent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Data Responden';
    protected static ?string $pluralModelLabel = 'Data Responden';
    protected static ?string $modelLabel = 'Responden';
    protected static ?string $navigationGroup = 'Data Kuesioner';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return RespondentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RespondentsTable::configure($table);
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
            'index' => ListRespondents::route('/'),
            'create' => CreateRespondent::route('/create'),
            'edit' => EditRespondent::route('/{record}/edit'),
        ];
    }
}
