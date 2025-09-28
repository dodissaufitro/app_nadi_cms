<?php

namespace App\Filament\Resources\TbBeritaResource\Pages;

use App\Filament\Resources\TbBeritaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTbBeritas extends ListRecords
{
    protected static string $resource = TbBeritaResource::class;

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
