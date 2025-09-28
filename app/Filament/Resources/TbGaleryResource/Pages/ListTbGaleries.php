<?php

namespace App\Filament\Resources\TbGaleryResource\Pages;

use App\Filament\Resources\TbGaleryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTbGaleries extends ListRecords
{
    protected static string $resource = TbGaleryResource::class;

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
