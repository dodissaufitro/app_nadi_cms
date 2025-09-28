<?php

namespace App\Filament\Resources\TbBeritaResource\Pages;

use App\Filament\Resources\TbBeritaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTbBerita extends EditRecord
{
    protected static string $resource = TbBeritaResource::class;

    protected static ?string $title = 'Edit Berita';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->color('info'),
            Actions\DeleteAction::make()
                ->color('danger'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Berita berhasil diperbarui!';
    }
}
