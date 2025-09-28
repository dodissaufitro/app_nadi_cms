<?php

namespace App\Filament\Resources\TbGaleryResource\Pages;

use App\Filament\Resources\TbGaleryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTbGalery extends ViewRecord
{
    protected static string $resource = TbGaleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
