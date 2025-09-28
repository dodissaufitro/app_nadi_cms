<?php

namespace App\Filament\Resources\TbIdentitasNagariResource\Pages;

use App\Filament\Resources\TbIdentitasNagariResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTbIdentitasNagari extends ViewRecord
{
    protected static string $resource = TbIdentitasNagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
