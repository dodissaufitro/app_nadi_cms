<?php

namespace App\Filament\Resources\TbIdentitasNagariResource\Pages;

use App\Filament\Resources\TbIdentitasNagariResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTbIdentitasNagaris extends ListRecords
{
    protected static string $resource = TbIdentitasNagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }
}
