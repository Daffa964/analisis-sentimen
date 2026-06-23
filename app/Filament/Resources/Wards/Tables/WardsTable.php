<?php

namespace App\Filament\Resources\Wards\Tables;

use App\Models\Ward;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class WardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->badge()
                    ->colors([
                        'warning' => 'vip',
                        'info' => 'regular',
                    ])
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->label('Kategori'),
                TextColumn::make('description')
                    ->limit(50)
                    ->label('Deskripsi'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading('QR Code Survei Bangsal')
                    ->modalContent(fn (Ward $record) => view(
                        'filament.components.qr-code-modal',
                        [
                            'url' => url('/survey/' . $record->qr_token),
                            'name' => $record->name
                        ]
                    ))
                    ->modalSubmitAction(false), // Hide the submit button in modal
                Action::make('copy_link')
                    ->label('Salin Link')
                    ->icon('heroicon-o-clipboard')
                    ->color('info')
                    ->extraAttributes(fn (Ward $record) => [
                        'x-on:click' => "window.navigator.clipboard.writeText('" . url('/survey/' . $record->qr_token) . "');",
                    ])
                    ->action(fn () => Notification::make()
                        ->title('Link kuesioner berhasil disalin ke clipboard!')
                        ->success()
                        ->send())
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
