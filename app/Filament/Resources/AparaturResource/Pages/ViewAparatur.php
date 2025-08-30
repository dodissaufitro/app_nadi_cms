<?php

namespace App\Filament\Resources\AparaturResource\Pages;

use App\Filament\Resources\AparaturResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAparatur extends ViewRecord
{
    protected static string $resource = AparaturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
