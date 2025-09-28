<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TbAparaturNagariResource\Pages;
use App\Filament\Resources\TbAparaturNagariResource\RelationManagers;
use App\Models\TbAparaturNagari;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TbAparaturNagariResource extends Resource
{
    protected static ?string $model = TbAparaturNagari::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Nagari';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Aparatur Nagari';

    protected static ?string $pluralModelLabel = 'Aparatur Nagari';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Aparatur Nagari')
                    ->description('Masukkan data lengkap aparatur nagari')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('Masukkan nama lengkap')
                                    ->prefixIcon('heroicon-o-user')
                                    ->columnSpan(2),

                                Forms\Components\Select::make('jabatan')
                                    ->label('Jabatan')
                                    ->required()
                                    ->options([
                                        'Wali Nagari' => 'Wali Nagari',
                                        'Sekretaris Nagari' => 'Sekretaris Nagari',
                                        'Kaur Pemerintahan' => 'Kaur Pemerintahan',
                                        'Kaur Pembangunan' => 'Kaur Pembangunan',
                                        'Kaur Kesejahteraan' => 'Kaur Kesejahteraan',
                                        'Kaur Keuangan' => 'Kaur Keuangan',
                                        'Kaur Umum' => 'Kaur Umum',
                                        'Kepala Jorong' => 'Kepala Jorong',
                                        'Anggota BPD' => 'Anggota BPD',
                                        'Ketua BPD' => 'Ketua BPD',
                                        'Lainnya' => 'Lainnya'
                                    ])
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('no_hp')
                                    ->label('Nomor HP')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('08xxxxxxxxxx')
                                    ->prefixIcon('heroicon-o-phone')
                                    ->mask('9999-9999-99999')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(150)
                                    ->placeholder('contoh@email.com')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('generateEmail')
                                            ->icon('heroicon-o-sparkles')
                                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                                $nama = $get('nama');
                                                if ($nama) {
                                                    $email = strtolower(str_replace(' ', '.', $nama)) . '@nagari.go.id';
                                                    $set('email', $email);
                                                }
                                            })
                                    ),
                            ]),
                    ]),

                Forms\Components\Section::make('Upload Foto')
                    ->description('Upload foto aparatur nagari')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto Aparatur')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                                '3:4',
                            ])
                            ->directory('aparatur-photos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload foto dengan ukuran maksimal 2MB. Format yang didukung: JPG, PNG, WEBP')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),
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
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->extraAttributes(['class' => 'ring-2 ring-gray-200']),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Nama berhasil disalin!')
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Wali Nagari' => 'danger',
                        'Sekretaris Nagari' => 'warning',
                        'Ketua BPD' => 'success',
                        default => 'gray',
                    })
                    ->icon('heroicon-o-briefcase'),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor HP berhasil disalin!')
                    ->icon('heroicon-o-phone')
                    ->url(fn($record) => $record->no_hp ? 'tel:' . $record->no_hp : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->icon('heroicon-o-envelope')
                    ->url(fn($record) => $record->email ? 'mailto:' . $record->email : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-pencil'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jabatan')
                    ->label('Filter Jabatan')
                    ->options([
                        'Wali Nagari' => 'Wali Nagari',
                        'Sekretaris Nagari' => 'Sekretaris Nagari',
                        'Kaur Pemerintahan' => 'Kaur Pemerintahan',
                        'Kaur Pembangunan' => 'Kaur Pembangunan',
                        'Kaur Kesejahteraan' => 'Kaur Kesejahteraan',
                        'Kaur Keuangan' => 'Kaur Keuangan',
                        'Kaur Umum' => 'Kaur Umum',
                        'Kepala Jorong' => 'Kepala Jorong',
                        'Anggota BPD' => 'Anggota BPD',
                        'Ketua BPD' => 'Ketua BPD',
                    ])
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ])
                    ->label('Aksi Massal'),
            ])

            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListTbAparaturNagaris::route('/'),

        ];
    }
}
