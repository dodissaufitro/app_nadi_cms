<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NagariResource\Pages;
use App\Filament\Resources\NagariResource\RelationManagers;
use App\Models\Nagari;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NagariResource extends Resource
{
    protected static ?string $model = Nagari::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Manajemen Nagari';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nagari')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('kecamatan')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('kabupaten')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('provinsi')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('kode_pos')
                    ->maxLength(10),
                Forms\Components\TextInput::make('gambar')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_penduduk')
                    ->numeric(),
                Forms\Components\TextInput::make('luas_wilayah')
                    ->numeric(),
                Forms\Components\TextInput::make('tahun_pembentukan')
                    ->numeric(),
                Forms\Components\Textarea::make('visi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('misi')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_pos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gambar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_penduduk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('luas_wilayah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun_pembentukan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListNagaris::route('/'),
            'create' => Pages\CreateNagari::route('/create'),
            'view' => Pages\ViewNagari::route('/{record}'),
            'edit' => Pages\EditNagari::route('/{record}/edit'),
        ];
    }
}
