<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TbIdentitasNagariResource\Pages;
use App\Filament\Resources\TbIdentitasNagariResource\RelationManagers;
use App\Models\TbIdentitasNagari;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TbIdentitasNagariResource extends Resource
{
    protected static ?string $model = TbIdentitasNagari::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Identitas Nagari';

    protected static ?string $modelLabel = 'Identitas Nagari';

    protected static ?string $pluralModelLabel = 'Identitas Nagari';

    protected static ?string $navigationGroup = 'Manajemen Nagari';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar Nagari')
                    ->description('Data identitas dan lokasi nagari')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Nagari')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama nagari')
                                    ->prefixIcon('heroicon-o-building-office'),
                                Forms\Components\TextInput::make('kecamatan')
                                    ->label('Kecamatan')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('Masukkan nama kecamatan')
                                    ->prefixIcon('heroicon-o-map'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('kabupaten')
                                    ->label('Kabupaten')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('Masukkan nama kabupaten')
                                    ->prefixIcon('heroicon-o-globe-asia-australia'),
                                Forms\Components\TextInput::make('provinsi')
                                    ->label('Provinsi')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('Masukkan nama provinsi')
                                    ->prefixIcon('heroicon-o-flag'),
                                Forms\Components\TextInput::make('kode_pos')
                                    ->label('Kode Pos')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('12345')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->maxLength(10),
                            ]),
                    ]),

                Forms\Components\Section::make('Data Demografis & Geografis')
                    ->description('Informasi penduduk dan wilayah')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_penduduk')
                                    ->label('Total Penduduk')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('0')
                                    ->prefixIcon('heroicon-o-users')
                                    ->suffix('jiwa'),
                                Forms\Components\TextInput::make('luas_wilayah')
                                    ->label('Luas Wilayah')
                                    ->required()
                                    ->numeric()
                                    ->step(0.01)
                                    ->placeholder('0.00')
                                    ->prefixIcon('heroicon-o-square-3-stack-3d')
                                    ->suffix('km²'),
                                Forms\Components\TextInput::make('tahun_pembentukan')
                                    ->label('Tahun Pembentukan')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('2000')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->minValue(1800)
                                    ->maxValue(date('Y')),
                            ]),
                    ]),

                Forms\Components\Section::make('Visi & Misi')
                    ->description('Visi dan misi nagari')
                    ->icon('heroicon-o-light-bulb')
                    ->schema([
                        Forms\Components\Textarea::make('visi')
                            ->label('Visi Nagari')
                            ->required()
                            ->rows(4)
                            ->placeholder('Masukkan visi nagari...')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('misi')
                            ->label('Misi Nagari')
                            ->required()
                            ->rows(6)
                            ->placeholder('Masukkan misi nagari...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Foto Nagari')
                    ->description('Upload foto representatif nagari')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto Nagari')
                            ->image()
                            ->directory('nagari')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('/images/default-nagari.png'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Nagari')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->label('Kabupaten')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->label('Provinsi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kode_pos')
                    ->label('Kode Pos')
                    ->badge()
                    ->color('gray')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_penduduk')
                    ->label('Penduduk')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => number_format($state) . ' jiwa')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('luas_wilayah')
                    ->label('Luas')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => number_format($state, 2) . ' km²')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('tahun_pembentukan')
                    ->label('Tahun Berdiri')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kecamatan')
                    ->label('Kecamatan')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('kabupaten')
                    ->label('Kabupaten')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('tahun_pembentukan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tahun')
                            ->label('Dari Tahun'),
                        Forms\Components\DatePicker::make('sampai_tahun')
                            ->label('Sampai Tahun'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tahun'],
                                fn(Builder $query, $date): Builder => $query->whereYear('tahun_pembentukan', '>=', date('Y', strtotime($date))),
                            )
                            ->when(
                                $data['sampai_tahun'],
                                fn(Builder $query, $date): Builder => $query->whereYear('tahun_pembentukan', '<=', date('Y', strtotime($date))),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum ada data identitas nagari')
            ->emptyStateDescription('Silakan tambahkan data identitas nagari terlebih dahulu.')
            ->emptyStateIcon('heroicon-o-identification');
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
            'index' => Pages\ListTbIdentitasNagaris::route('/'),
            'create' => Pages\CreateTbIdentitasNagari::route('/create'),
            'view' => Pages\ViewTbIdentitasNagari::route('/{record}'),
            'edit' => Pages\EditTbIdentitasNagari::route('/{record}/edit'),
        ];
    }
}
