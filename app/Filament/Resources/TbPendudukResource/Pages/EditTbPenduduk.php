<?php

namespace App\Filament\Resources\TbPendudukResource\Pages;

use App\Filament\Resources\TbPendudukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTbPenduduk extends EditRecord
{
    protected static string $resource = TbPendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
