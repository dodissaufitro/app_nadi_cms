<?php

namespace App\Filament\Resources\TbAparaturNagariResource\Pages;

use App\Filament\Resources\TbAparaturNagariResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTbAparaturNagari extends ViewRecord
{
    protected static string $resource = TbAparaturNagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
