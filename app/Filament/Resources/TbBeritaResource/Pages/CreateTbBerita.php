<?php

namespace App\Filament\Resources\TbBeritaResource\Pages;

use App\Filament\Resources\TbBeritaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTbBerita extends CreateRecord
{
    protected static string $resource = TbBeritaResource::class;

    protected static ?string $title = 'Buat Berita Baru';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Berita berhasil dibuat!';
    }
}
