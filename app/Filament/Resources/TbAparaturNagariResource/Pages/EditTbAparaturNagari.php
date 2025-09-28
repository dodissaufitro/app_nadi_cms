<?php

namespace App\Filament\Resources\TbAparaturNagariResource\Pages;

use App\Filament\Resources\TbAparaturNagariResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTbAparaturNagari extends EditRecord
{
    protected static string $resource = TbAparaturNagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
