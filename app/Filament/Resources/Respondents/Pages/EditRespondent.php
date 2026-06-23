<?php

namespace App\Filament\Resources\Respondents\Pages;

use App\Filament\Resources\Respondents\RespondentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRespondent extends EditRecord
{
    protected static string $resource = RespondentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
