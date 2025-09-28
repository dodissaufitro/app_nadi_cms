<?php

namespace App\Filament\Resources\TbAparaturNagariResource\Pages;

use App\Filament\Resources\TbAparaturNagariResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTbAparaturNagaris extends ListRecords
{
    protected static string $resource = TbAparaturNagariResource::class;

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
