<?php

namespace App\Filament\Resources\TbPendudukResource\Pages;

use App\Filament\Resources\TbPendudukResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewTbPenduduk extends ViewRecord
{
    protected static string $resource = TbPendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Data')
                ->color('warning')
                ->icon('heroicon-o-pencil-square'),
            Actions\DeleteAction::make()
                ->label('Hapus Data')
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('IDENTITAS PENDUDUK')
                    ->icon('heroicon-o-identification')
                    ->iconColor('slate')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('nik')
                                        ->label('NIK')
                                        ->copyable()
                                        ->copyMessage('NIK disalin!')
                                        ->fontFamily('mono')
                                        ->weight('bold')
                                        ->size('lg'),
                                    Infolists\Components\TextEntry::make('nama')
                                        ->label('Nama Lengkap')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),
                                    Infolists\Components\TextEntry::make('no_kk')
                                        ->label('No. Kartu Keluarga')
                                        ->copyable()
                                        ->fontFamily('mono'),
                                    Infolists\Components\TextEntry::make('tgl_terdaftar')
                                        ->label('Tanggal Terdaftar')
                                        ->date('d F Y'),
                                    Infolists\Components\TextEntry::make('nama_ayah')
                                        ->label('Nama Ayah')
                                        ->placeholder('Tidak ada data'),
                                    Infolists\Components\TextEntry::make('nama_ibu')
                                        ->label('Nama Ibu')
                                        ->placeholder('Tidak ada data'),
                                ]),
                            Infolists\Components\ImageEntry::make('foto')
                                ->label('Foto Penduduk')
                                ->defaultImageUrl('/images/default-avatar.png')
                                ->height(200)
                                ->width(150)
                                ->extraImgAttributes(['class' => 'rounded-lg shadow-lg']),
                        ]),
                    ]),

                Infolists\Components\Section::make('ALAMAT DAN DOMISILI')
                    ->icon('heroicon-o-home')
                    ->iconColor('slate')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('alamat')
                                    ->label('Alamat Lengkap')
                                    ->columnSpan(3),
                                Infolists\Components\TextEntry::make('dusun')
                                    ->label('Dusun')
                                    ->badge()
                                    ->color('info'),
                                Infolists\Components\TextEntry::make('rt')
                                    ->label('RT')
                                    ->badge()
                                    ->color('success')
                                    ->formatStateUsing(fn($state) => "RT {$state}"),
                                Infolists\Components\TextEntry::make('rw')
                                    ->label('RW')
                                    ->badge()
                                    ->color('warning')
                                    ->formatStateUsing(fn($state) => "RW {$state}"),
                            ]),
                    ]),

                Infolists\Components\Section::make('DATA DEMOGRAFI')
                    ->icon('heroicon-o-chart-bar-square')
                    ->iconColor('slate')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('umur')
                                    ->label('Usia')
                                    ->suffix(' tahun')
                                    ->badge()
                                    ->color(fn(int $state): string => match (true) {
                                        $state < 17 => 'yellow',
                                        $state >= 60 => 'blue',
                                        default => 'green',
                                    }),
                                Infolists\Components\TextEntry::make('pendidikan')
                                    ->label('Pendidikan')
                                    ->badge()
                                    ->color(fn(string $state): string => match (true) {
                                        str_contains($state, 'STRATA') => 'green',
                                        str_contains($state, 'DIPLOMA') => 'blue',
                                        str_contains($state, 'SLTA') => 'yellow',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('pekerjaan')
                                    ->label('Pekerjaan')
                                    ->badge()
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('status_pernikahan')
                                    ->label('Status Perkawinan')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'menikah' => 'green',
                                        'belum_menikah' => 'blue',
                                        'janda' => 'orange',
                                        'duda' => 'red',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'belum_menikah' => 'Belum Kawin',
                                        'menikah' => 'Kawin',
                                        'janda' => 'Cerai Hidup',
                                        'duda' => 'Cerai Mati',
                                        default => $state,
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('INFORMASI SISTEM')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('gray')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat pada')
                                    ->dateTime('d F Y, H:i:s')
                                    ->color('gray'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Terakhir diperbarui')
                                    ->dateTime('d F Y, H:i:s')
                                    ->color('gray'),
                            ]),
                    ]),
            ]);
    }
}
