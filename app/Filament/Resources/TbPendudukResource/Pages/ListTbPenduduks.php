<?php

namespace App\Filament\Resources\TbPendudukResource\Pages;

use App\Filament\Resources\TbPendudukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTbPenduduks extends ListRecords
{
    protected static string $resource = TbPendudukResource::class;

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
