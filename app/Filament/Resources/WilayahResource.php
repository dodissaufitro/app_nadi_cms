<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WilayahResource\Pages;
use App\Filament\Resources\WilayahResource\RelationManagers;
use App\Models\Wilayah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WilayahResource extends Resource
{
    protected static ?string $model = Wilayah::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Data Wilayah';

    protected static ?string $modelLabel = 'Wilayah';

    protected static ?string $pluralModelLabel = 'Data Wilayah';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Manajemen Nagari';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('🏘️ Informasi Wilayah')
                    ->description('Data administrasi wilayah')
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('emerald')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('dusun')
                                    ->label('🏠 Nama Dusun')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama dusun')
                                    ->prefixIcon('heroicon-o-home')
                                    ->prefixIconColor('emerald')
                                    ->helperText('Contoh: Dusun Mawar, Dusun Melati'),
                                Forms\Components\TextInput::make('kawil')
                                    ->label('👥 Kawil')
                                    ->maxLength(255)
                                    ->placeholder('Masukkan kawil')
                                    ->prefixIcon('heroicon-o-user-group')
                                    ->prefixIconColor('blue')
                                    ->helperText('Kepala Wilayah'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('rw')
                                    ->label('🏘️ RW')
                                    ->maxLength(255)
                                    ->placeholder('001')
                                    ->prefixIcon('heroicon-o-squares-2x2')
                                    ->prefixIconColor('green')
                                    ->mask('999'),
                                Forms\Components\TextInput::make('rt')
                                    ->label('🏠 RT')
                                    ->maxLength(255)
                                    ->placeholder('001')
                                    ->prefixIcon('heroicon-o-squares-plus')
                                    ->prefixIconColor('orange')
                                    ->mask('999'),
                                Forms\Components\TextInput::make('kk')
                                    ->label('📄 Jumlah KK')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->prefixIconColor('purple')
                                    ->suffix('KK')
                                    ->helperText('Jumlah Kepala Keluarga'),
                            ]),
                    ])
                    ->columnSpan(2),

                Forms\Components\Section::make('👤 Data Kepala Wilayah')
                    ->description('Informasi kepala wilayah')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('indigo')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nik_kawil')
                                    ->label('🆔 NIK Kepala Wilayah')
                                    ->maxLength(16)
                                    ->placeholder('1234567890123456')
                                    ->prefixIcon('heroicon-o-identification')
                                    ->prefixIconColor('red')
                                    ->mask('9999999999999999')
                                    ->helperText('16 digit NIK'),
                                Forms\Components\TextInput::make('nama_kawil')
                                    ->label('👨‍💼 Nama Kepala Wilayah')
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap')
                                    ->prefixIcon('heroicon-o-user')
                                    ->prefixIconColor('indigo')
                                    ->helperText('Nama lengkap sesuai KTP'),
                            ]),
                    ])
                    ->columnSpan(2),

                Forms\Components\Section::make('👥 Data Demografis')
                    ->description('Data penduduk berdasarkan jenis kelamin')
                    ->icon('heroicon-o-chart-bar')
                    ->iconColor('cyan')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('laki_laki')
                                    ->label('♂️ Laki-laki')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->prefixIcon('heroicon-o-user')
                                    ->prefixIconColor('blue')
                                    ->suffix('orang')
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('perempuan')
                                    ->label('♀️ Perempuan')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->prefixIcon('heroicon-o-user')
                                    ->prefixIconColor('pink')
                                    ->suffix('orang')
                                    ->live(onBlur: true),
                                Forms\Components\Placeholder::make('total_penduduk')
                                    ->label('👥 Total Penduduk')
                                    ->content(function ($get) {
                                        $laki = (int) $get('laki_laki') ?? 0;
                                        $perempuan = (int) $get('perempuan') ?? 0;
                                        $total = $laki + $perempuan;
                                        return new \Illuminate\Support\HtmlString(
                                            '<span class="text-lg font-bold text-green-600">' .
                                                number_format($total) . ' orang</span>'
                                        );
                                    }),
                            ]),
                    ])
                    ->columnSpan(2),

                Forms\Components\Section::make('📍 Koordinat Lokasi')
                    ->description('Koordinat GPS wilayah untuk pemetaan')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('red')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('🌐 Latitude')
                                    ->placeholder('-0.123456')
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->prefixIconColor('emerald')
                                    ->step(0.000001)
                                    ->helperText('Koordinat garis lintang'),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('🌐 Longitude')
                                    ->placeholder('100.123456')
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->prefixIconColor('emerald')
                                    ->step(0.000001)
                                    ->helperText('Koordinat garis bujur'),
                            ]),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dusun')
                    ->label('Dusun')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-home')
                    ->iconColor('emerald'),
                Tables\Columns\TextColumn::make('kawil')
                    ->label('Kawil')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Kawil 1' => 'success',
                        'Kawil 2' => 'info',
                        'Kawil 3' => 'warning',
                        'Kawil 4' => 'danger',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('nama_kawil')
                    ->label('Kepala Wilayah')
                    ->searchable()
                    ->wrap()
                    ->limit(30)
                    ->color('indigo')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('rw')
                    ->label('RW')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->prefix('RW '),
                Tables\Columns\TextColumn::make('rt')
                    ->label('RT')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->prefix('RT '),
                Tables\Columns\TextColumn::make('kk')
                    ->label('KK')
                    ->formatStateUsing(fn(string $state): string => number_format($state) . ' KK')
                    ->alignEnd()
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('laki_laki')
                    ->label('♂ Laki-laki')
                    ->formatStateUsing(fn(string $state): string => number_format($state))
                    ->alignEnd()
                    ->sortable()
                    ->color('blue')
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('perempuan')
                    ->label('♀ Perempuan')
                    ->formatStateUsing(fn(string $state): string => number_format($state))
                    ->alignEnd()
                    ->sortable()
                    ->color('pink')
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('total_penduduk')
                    ->label('👥 Total')
                    ->getStateUsing(function ($record) {
                        return (int)$record->laki_laki + (int)$record->perempuan;
                    })
                    ->formatStateUsing(fn(int $state): string => number_format($state))
                    ->alignEnd()
                    ->weight('bold')
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state >= 500 => 'success',
                        $state >= 200 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\IconColumn::make('has_coordinates')
                    ->label('📍 GPS')
                    ->getStateUsing(fn($record) => !empty($record->latitude) && !empty($record->longitude))
                    ->boolean()
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->size('lg'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dusun')
                    ->label('🏘️ Filter Dusun')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('rw')
                    ->label('🏠 Filter RW')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_coordinates')
                    ->label('📍 Memiliki Koordinat GPS')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude'))
                    ->toggle(),
                Tables\Filters\Filter::make('jumlah_penduduk')
                    ->label('👥 Filter Jumlah Penduduk')
                    ->form([
                        Forms\Components\TextInput::make('min_penduduk')
                            ->label('Minimal Penduduk')
                            ->numeric()
                            ->prefixIcon('heroicon-o-arrow-up'),
                        Forms\Components\TextInput::make('max_penduduk')
                            ->label('Maksimal Penduduk')
                            ->numeric()
                            ->prefixIcon('heroicon-o-arrow-down'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_penduduk'],
                                fn(Builder $query, $min): Builder => $query->whereRaw('CAST(laki_laki AS UNSIGNED) + CAST(perempuan AS UNSIGNED) >= ?', [$min])
                            )
                            ->when(
                                $data['max_penduduk'],
                                fn(Builder $query, $max): Builder => $query->whereRaw('CAST(laki_laki AS UNSIGNED) + CAST(perempuan AS UNSIGNED) <= ?', [$max])
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->color('danger'),
                ])
                    ->label('Aksi Massal'),
            ])
            ->defaultSort('dusun', 'asc')
            ->striped()
            ->emptyStateHeading('🏘️ Belum ada data wilayah')
            ->emptyStateDescription('Silakan tambahkan data wilayah terlebih dahulu untuk memulai.')
            ->emptyStateIcon('heroicon-o-map')
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWilayahs::route('/'),
            'create' => Pages\CreateWilayah::route('/create'),
            'view' => Pages\ViewWilayah::route('/{record}'),
            'edit' => Pages\EditWilayah::route('/{record}/edit'),
        ];
    }
}
