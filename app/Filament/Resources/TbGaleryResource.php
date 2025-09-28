<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TbGaleryResource\Pages;
use App\Filament\Resources\TbGaleryResource\RelationManagers;
use App\Models\TbGalery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TbGaleryResource extends Resource
{
    protected static ?string $model = TbGalery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $modelLabel = 'Gallery Item';

    protected static ?string $pluralModelLabel = 'Gallery';

    protected static ?string $navigationGroup = 'Manajemen Nagari';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gallery Information')
                    ->description('Add gallery item details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('kategori')
                                    ->label('Category')
                                    ->options([
                                        'event' => 'Event',
                                        'kegiatan' => 'Kegiatan',
                                        'fasilitas' => 'Fasilitas',
                                        'prestasi' => 'Prestasi',
                                        'lainnya' => 'Lainnya',
                                    ])
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('judul')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter gallery title'),
                            ]),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Description')
                            ->required()
                            ->rows(4)
                            ->placeholder('Enter description for this gallery item')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Image')
                    ->description('Upload gallery image')
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->label('Photo')
                            ->image()
                            ->directory('gallery')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Photo')
                    ->square()
                    ->size(60),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'event' => 'success',
                        'kegiatan' => 'info',
                        'fasilitas' => 'warning',
                        'prestasi' => 'danger',
                        'lainnya' => 'gray',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Description')
                    ->limit(100)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 100) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Category')
                    ->options([
                        'event' => 'Event',
                        'kegiatan' => 'Kegiatan',
                        'fasilitas' => 'Fasilitas',
                        'prestasi' => 'Prestasi',
                        'lainnya' => 'Lainnya',
                    ])
                    ->multiple(),
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
            ->emptyStateHeading('No gallery items yet')
            ->emptyStateDescription('Once you add gallery items, they will appear here.');
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
            'index' => Pages\ListTbGaleries::route('/'),
            'create' => Pages\CreateTbGalery::route('/create'),
            'view' => Pages\ViewTbGalery::route('/{record}'),
            'edit' => Pages\EditTbGalery::route('/{record}/edit'),
        ];
    }
}
