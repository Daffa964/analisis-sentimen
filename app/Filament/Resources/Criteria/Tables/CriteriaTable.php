<?php

namespace App\Filament\Resources\Criteria\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CriteriaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label('Kode Kriteria')
                    ->weight('bold'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Kriteria'),
                TextColumn::make('ahpWeight.weight')
                    ->label('Bobot AHP')
                    ->numeric(4)
                    ->default(0.0000)
                    ->color('success')
                    ->weight('semibold')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // Criteria are usually static master data, disable bulk delete to prevent breaking references
            ]);
    }
}
