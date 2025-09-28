<?php

namespace App\Filament\Resources\TbGaleryResource\Pages;

use App\Filament\Resources\TbGaleryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTbGalery extends EditRecord
{
    protected static string $resource = TbGaleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
