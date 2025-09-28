<?php

namespace App\Filament\Resources\TbIdentitasNagariResource\Pages;

use App\Filament\Resources\TbIdentitasNagariResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTbIdentitasNagari extends EditRecord
{
    protected static string $resource = TbIdentitasNagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
