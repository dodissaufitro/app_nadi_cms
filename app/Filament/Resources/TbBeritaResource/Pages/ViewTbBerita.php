<?php

namespace App\Filament\Resources\TbBeritaResource\Pages;

use App\Filament\Resources\TbBeritaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTbBerita extends ViewRecord
{
    protected static string $resource = TbBeritaResource::class;

    protected static ?string $title = 'Lihat Berita';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning'),
            Actions\DeleteAction::make()
                ->color('danger'),
        ];
    }
}
