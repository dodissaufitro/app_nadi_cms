<?php

namespace App\Filament\Resources\NagariResource\Pages;

use App\Filament\Resources\NagariResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNagari extends EditRecord
{
    protected static string $resource = NagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
