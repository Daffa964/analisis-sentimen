<?php

namespace App\Filament\Resources\Respondents\Tables;

use App\Models\Respondent;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RespondentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('surveyPeriod.name')
                    ->label('Periode')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ward.name')
                    ->label('Bangsal')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('respondent_name')
                    ->label('Nama Responden')
                    ->default('Anonim')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->colors([
                        'info' => 'Laki-laki',
                        'danger' => 'Perempuan',
                    ])
                    ->default('-'),
                TextColumn::make('age_group')
                    ->label('Usia')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => $state ? $state . ' Thn' : '-')
                    ->default('-'),
                TextColumn::make('created_at')
                    ->label('Tanggal Isi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view_answers')
                    ->label('Lihat Jawaban')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->modalHeading(fn (Respondent $record) => 'Jawaban Kuesioner: ' . ($record->respondent_name ?: 'Anonim'))
                    ->modalContent(fn (Respondent $record) => view(
                        'filament.components.respondent-answers-modal',
                        ['respondent' => $record->load('surveyAnswers.question.criterion')]
                    ))
                    ->modalSubmitAction(false), // Hide the submit button in modal
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
