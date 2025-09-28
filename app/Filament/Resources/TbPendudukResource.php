<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TbPendudukResource\Pages;
use App\Filament\Resources\TbPendudukResource\RelationManagers;
use App\Models\TbPenduduk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TbPendudukResource extends Resource
{
    protected static ?string $model = TbPenduduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Data Kependudukan';

    protected static ?string $modelLabel = 'Penduduk';

    protected static ?string $pluralModelLabel = 'Data Kependudukan';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Manajemen Nagari';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('DATA IDENTITAS PENDUDUK')
                    ->description('Informasi identitas berdasarkan dokumen kependudukan yang sah')
                    ->icon('heroicon-o-identification')
                    ->iconColor('slate')
                    ->collapsed(false)
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK (Nomor Induk Kependudukan)')
                                    ->required()
                                    ->maxLength(16)
                                    ->placeholder('Masukkan 16 digit NIK')
                                    ->prefixIcon('heroicon-o-credit-card')
                                    ->mask('9999999999999999')
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Sesuai dengan KTP elektronik yang berlaku')
                                    ->validationMessages([
                                        'required' => 'NIK wajib diisi',
                                        'unique' => 'NIK sudah terdaftar dalam sistem',
                                    ]),
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap sesuai KTP')
                                    ->prefixIcon('heroicon-o-user')
                                    ->helperText('Nama lengkap tanpa gelar, sesuai dokumen resmi')
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                            ]),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->required()
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->prefixIcon('heroicon-o-user-circle'),
                                Forms\Components\DatePicker::make('tgl_lahir')
                                    ->label('Tanggal Lahir')
                                    ->required()
                                    ->maxDate(now())
                                    ->prefixIcon('heroicon-o-cake')
                                    ->helperText('Tanggal lahir sesuai KTP')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $birthDate = \Carbon\Carbon::parse($state);
                                            $age = $birthDate->age;
                                            $set('umur', $age);
                                        }
                                    }),
                                Forms\Components\TextInput::make('tempat_lahir')
                                    ->label('Tempat Lahir')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Kota/Kabupaten kelahiran')
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                                Forms\Components\TextInput::make('umur')
                                    ->label('Usia (Tahun)')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('0')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->suffix('tahun')
                                    ->minValue(0)
                                    ->maxValue(150)
                                    ->helperText('Otomatis dihitung dari tanggal lahir')
                                    ->readOnly()
                                    ->default(0),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('no_kk')
                                    ->label('Nomor Kartu Keluarga')
                                    ->required()
                                    ->maxLength(16)
                                    ->placeholder('Masukkan 16 digit No. KK')
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->mask('9999999999999999')
                                    ->helperText('Nomor Kartu Keluarga yang tercantum di KK'),
                                Forms\Components\TextInput::make('nama_ayah')
                                    ->label('Nama Ayah Kandung')
                                    ->maxLength(255)
                                    ->placeholder('Nama ayah kandung')
                                    ->prefixIcon('heroicon-o-user')
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                                Forms\Components\TextInput::make('nama_ibu')
                                    ->label('Nama Ibu Kandung')
                                    ->maxLength(255)
                                    ->placeholder('Nama ibu kandung')
                                    ->prefixIcon('heroicon-o-user')
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                            ]),
                    ]),

                Forms\Components\Section::make('DOKUMENTASI PENDUDUK')
                    ->description('Upload foto resmi penduduk untuk keperluan administrasi')
                    ->icon('heroicon-o-camera')
                    ->iconColor('slate')
                    ->collapsed(true)
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto Resmi Penduduk')
                            ->image()
                            ->directory('kependudukan/foto-penduduk')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '3:4',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->columnSpanFull()
                            ->helperText('Format: JPG/PNG, Maksimal 2MB. Gunakan foto formal dengan latar belakang yang sesuai'),
                    ]),

                Forms\Components\Section::make('ALAMAT DAN DOMISILI')
                    ->description('Informasi tempat tinggal berdasarkan administrasi wilayah')
                    ->icon('heroicon-o-home')
                    ->iconColor('slate')
                    ->collapsed(false)
                    ->schema([
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->placeholder('Masukkan alamat lengkap sesuai KTP')
                            ->columnSpanFull()
                            ->helperText('Alamat sesuai dengan yang tercantum dalam KTP')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('dusun')
                                    ->label('Dusun/Lingkungan')
                                    ->required()
                                    ->searchable()
                                    ->options([
                                        'Dusun I' => 'Dusun I',
                                        'Dusun II' => 'Dusun II',
                                        'Dusun III' => 'Dusun III',
                                        'Dusun IV' => 'Dusun IV',
                                        'Dusun V' => 'Dusun V',
                                    ])
                                    ->prefixIcon('heroicon-o-building-office-2'),
                                Forms\Components\TextInput::make('rt')
                                    ->label('RT (Rukun Tetangga)')
                                    ->required()
                                    ->maxLength(10)
                                    ->placeholder('000')
                                    ->prefixIcon('heroicon-o-home-modern')
                                    ->mask('999')
                                    ->prefix('RT '),
                                Forms\Components\TextInput::make('rw')
                                    ->label('RW (Rukun Warga)')
                                    ->required()
                                    ->maxLength(10)
                                    ->placeholder('000')
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->mask('999')
                                    ->prefix('RW '),
                                Forms\Components\DatePicker::make('tgl_terdaftar')
                                    ->label('Tanggal Pendaftaran')
                                    ->required()
                                    ->default(now())
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->helperText('Tanggal pendaftaran dalam sistem'),
                            ]),
                    ]),

                Forms\Components\Section::make('DATA DEMOGRAFI')
                    ->description('Informasi demografis dan sosial ekonomi penduduk')
                    ->icon('heroicon-o-chart-bar-square')
                    ->iconColor('slate')
                    ->collapsed(false)
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('pendidikan')
                                    ->label('Tingkat Pendidikan')
                                    ->required()
                                    ->options([
                                        'TIDAK/BELUM SEKOLAH' => 'Tidak/Belum Sekolah',
                                        'TIDAK TAMAT SD/SEDERAJAT' => 'Tidak Tamat SD/Sederajat',
                                        'TAMAT SD/SEDERAJAT' => 'Tamat SD/Sederajat',
                                        'SLTP/SEDERAJAT' => 'SLTP/Sederajat',
                                        'SLTA/SEDERAJAT' => 'SLTA/Sederajat',
                                        'DIPLOMA I/II' => 'Diploma I/II',
                                        'AKADEMI/DIPLOMA III/S.MUDA' => 'Akademi/Diploma III/S.Muda',
                                        'DIPLOMA IV/STRATA I' => 'Diploma IV/Strata I',
                                        'STRATA II' => 'Strata II',
                                        'STRATA III' => 'Strata III',
                                    ])
                                    ->prefixIcon('heroicon-o-academic-cap')
                                    ->searchable(),
                                Forms\Components\TextInput::make('pekerjaan')
                                    ->label('Jenis Pekerjaan')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Sebutkan jenis pekerjaan')
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->helperText('Pekerjaan utama/sumber penghasilan')
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                                Forms\Components\Select::make('status_pernikahan')
                                    ->label('Status Perkawinan')
                                    ->required()
                                    ->options([
                                        'belum_menikah' => 'Belum Kawin',
                                        'menikah' => 'Kawin',
                                        'janda' => 'Cerai Hidup',
                                        'duda' => 'Cerai Mati',
                                    ])
                                    ->prefixIcon('heroicon-o-heart'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl('/images/default-avatar.png'),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIK berhasil disalin!')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->color('slate'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->color('slate')
                    ->limit(25)
                    ->tooltip(fn($record) => $record->nama),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'L' => 'blue',
                        'P' => 'pink',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => $state)
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('tempat_tgl_lahir')
                    ->label('TTL')
                    ->getStateUsing(fn($record) => $record->tempat_lahir . ', ' . \Carbon\Carbon::parse($record->tgl_lahir)->format('d/m/Y'))
                    ->limit(20)
                    ->tooltip(fn($record) => $record->tempat_lahir . ', ' . \Carbon\Carbon::parse($record->tgl_lahir)->format('d F Y')),
                Tables\Columns\TextColumn::make('umur')
                    ->label('Usia')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state < 17 => 'yellow',
                        $state >= 60 => 'blue',
                        default => 'green',
                    })
                    ->suffix(' th')
                    ->tooltip(fn(int $state): string => match (true) {
                        $state < 17 => 'Belum Dewasa',
                        $state >= 60 => 'Lansia',
                        default => 'Usia Produktif',
                    }),
                Tables\Columns\TextColumn::make('alamat_wilayah')
                    ->label('Wilayah Administrasi')
                    ->getStateUsing(fn($record) => "RT {$record->rt} / RW {$record->rw}")
                    ->badge()
                    ->color('slate'),
                Tables\Columns\TextColumn::make('dusun')
                    ->label('Dusun')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->label('Pendidikan')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        str_contains($state, 'STRATA') => 'green',
                        str_contains($state, 'DIPLOMA') => 'blue',
                        str_contains($state, 'SLTA') => 'yellow',
                        default => 'gray',
                    })
                    ->limit(15),
                Tables\Columns\TextColumn::make('pekerjaan')
                    ->label('Pekerjaan')
                    ->searchable()
                    ->limit(15)
                    ->tooltip(fn($record) => $record->pekerjaan),
                Tables\Columns\TextColumn::make('status_pernikahan')
                    ->label('Status Kawin')
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
                Tables\Columns\TextColumn::make('tgl_terdaftar')
                    ->label('Tgl. Daftar')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Filter Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                Tables\Filters\SelectFilter::make('dusun')
                    ->label('Filter Dusun')
                    ->options([
                        'Dusun I' => 'Dusun I',
                        'Dusun II' => 'Dusun II',
                        'Dusun III' => 'Dusun III',
                        'Dusun IV' => 'Dusun IV',
                        'Dusun V' => 'Dusun V',
                    ]),
                Tables\Filters\SelectFilter::make('pendidikan')
                    ->label('Filter Pendidikan')
                    ->options([
                        'TIDAK/BELUM SEKOLAH' => 'Tidak/Belum Sekolah',
                        'TIDAK TAMAT SD/SEDERAJAT' => 'Tidak Tamat SD',
                        'TAMAT SD/SEDERAJAT' => 'Tamat SD',
                        'SLTP/SEDERAJAT' => 'SLTP',
                        'SLTA/SEDERAJAT' => 'SLTA',
                        'DIPLOMA I/II' => 'Diploma I/II',
                        'AKADEMI/DIPLOMA III/S.MUDA' => 'D3/Akademi',
                        'DIPLOMA IV/STRATA I' => 'D4/S1',
                        'STRATA II' => 'S2',
                        'STRATA III' => 'S3',
                    ]),
                Tables\Filters\SelectFilter::make('status_pernikahan')
                    ->label('Filter Status Kawin')
                    ->options([
                        'belum_menikah' => 'Belum Kawin',
                        'menikah' => 'Kawin',
                        'janda' => 'Cerai Hidup',
                        'duda' => 'Cerai Mati',
                    ]),
                Tables\Filters\Filter::make('kelompok_usia')
                    ->label('Filter Kelompok Usia')
                    ->form([
                        Forms\Components\Select::make('kelompok')
                            ->label('Kelompok Usia')
                            ->options([
                                'anak' => 'Anak-anak (0-16 tahun)',
                                'produktif' => 'Usia Produktif (17-59 tahun)',
                                'lansia' => 'Lansia (60+ tahun)',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['kelompok'] ?? null) {
                            'anak' => $query->where('umur', '<=', 16),
                            'produktif' => $query->whereBetween('umur', [17, 59]),
                            'lansia' => $query->where('umur', '>=', 60),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->tooltip('Lihat Detail')
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit Data')
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Hapus Data')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih')
                        ->color('danger'),
                ])
                    ->label('Aksi Massal'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('BELUM ADA DATA KEPENDUDUKAN')
            ->emptyStateDescription('Silakan tambahkan data penduduk melalui formulir pendaftaran.')
            ->emptyStateIcon('heroicon-o-identification')
            ->paginated([25, 50, 100, 200])
            ->deferLoading()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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
            'index' => Pages\ListTbPenduduks::route('/'),
            'create' => Pages\CreateTbPenduduk::route('/create'),
            'view' => Pages\ViewTbPenduduk::route('/{record}'),
            'edit' => Pages\EditTbPenduduk::route('/{record}/edit'),
        ];
    }
}
