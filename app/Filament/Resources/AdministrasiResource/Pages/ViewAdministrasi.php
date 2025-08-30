<?php

namespace App\Filament\Resources\AdministrasiResource\Pages;

use App\Filament\Resources\AdministrasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdministrasi extends ViewRecord
{
    protected static string $resource = AdministrasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
