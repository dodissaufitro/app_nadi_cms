<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TbBeritaResource\Pages;
use App\Filament\Resources\TbBeritaResource\RelationManagers;
use App\Models\TbBerita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TbBeritaResource extends Resource
{
    protected static ?string $model = TbBerita::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Berita';

    protected static ?string $pluralModelLabel = 'Berita';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Berita')
                    ->description('Masukkan informasi dasar berita')
                    ->icon('heroicon-o-newspaper')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul Berita')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan judul berita yang menarik')
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->columnSpan(2),

                                Forms\Components\Textarea::make('konten')
                                    ->label('Konten Singkat')
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder('Masukkan ringkasan atau lead berita')
                                    ->rows(3)
                                    ->columnSpan(2),

                                Forms\Components\DateTimePicker::make('tanggal')
                                    ->label('Tanggal & Waktu Publikasi')
                                    ->required()
                                    ->default(now())
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('status')
                                    ->label('Status Publikasi')
                                    ->required()
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending' => 'Menunggu Review',
                                        'published' => 'Dipublikasikan',
                                        'archived' => 'Diarsipkan',
                                    ])
                                    ->default('draft')
                                    ->prefixIcon('heroicon-o-flag')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Forms\Components\Section::make('Konten Berita')
                    ->description('Tulis konten lengkap berita')
                    ->icon('heroicon-o-document')
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi Lengkap')
                            ->required()
                            ->placeholder('Tulis konten berita lengkap di sini...')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media & Publikasi')
                    ->description('Upload foto dan atur informasi publikasi')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('foto')
                                    ->label('Foto Berita')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->directory('berita-photos')
                                    ->visibility('public')
                                    ->maxSize(5120)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('Upload foto dengan ukuran maksimal 5MB. Format: JPG, PNG, WEBP')
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('created_by')
                                    ->default(1),

                                Forms\Components\Placeholder::make('info_author')
                                    ->label('Dibuat Oleh')
                                    ->content('Admin')
                                    ->columnSpan(1),
                            ]),
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
                // Card-like layout dengan gambar dan konten
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        // Left side - Image dengan overlay status
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\ImageColumn::make('foto')
                                ->label('')
                                ->height(100)
                                ->width(140)
                                ->extraAttributes([
                                    'class' => 'rounded-lg shadow-md object-cover border border-gray-200'
                                ])
                                ->defaultImageUrl('https://via.placeholder.com/280x200/f3f4f6/9ca3af?text=📰+Berita'),
                        ])->grow(false),

                        // Right side - Content details
                        Tables\Columns\Layout\Stack::make([
                            // Header with title and status
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\TextColumn::make('judul')
                                    ->searchable()
                                    ->sortable()
                                    ->weight('bold')
                                    ->size('xl')
                                    ->color('primary')
                                    ->limit(60)
                                    ->wrap()
                                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                                        $state = $column->getState();
                                        return strlen($state) > 60 ? $state : null;
                                    }),

                                Tables\Columns\TextColumn::make('status')
                                    ->badge()
                                    ->size('sm')
                                    ->alignEnd()
                                    ->color(fn(string $state): string => match ($state) {
                                        'published' => 'success',
                                        'pending' => 'warning',
                                        'draft' => 'info',
                                        'archived' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'published' => '🚀 LIVE',
                                        'pending' => '⏳ REVIEW',
                                        'draft' => '📝 DRAFT',
                                        'archived' => '📦 ARSIP',
                                        default => strtoupper($state),
                                    })
                                    ->grow(false),
                            ]),

                            // Content preview
                            Tables\Columns\TextColumn::make('konten')
                                ->label('')
                                ->searchable()
                                ->limit(120)
                                ->color('gray')
                                ->size('sm')
                                ->html()
                                ->formatStateUsing(fn(string $state): string => '📄 ' . strip_tags($state))
                                ->description(fn($record) => $record->deskripsi ? '💬 ' . \Illuminate\Support\Str::limit(strip_tags($record->deskripsi), 80) . '...' : ''),

                            // Meta information row
                            Tables\Columns\Layout\Grid::make(4)
                                ->schema([
                                    Tables\Columns\TextColumn::make('tanggal')
                                        ->label('')
                                        ->dateTime('d M Y')
                                        ->sortable()
                                        ->icon('heroicon-o-calendar-days')
                                        ->iconColor('info')
                                        ->size('xs')
                                        ->color('gray')
                                        ->prefix('📅 ')
                                        ->tooltip('Tanggal Publikasi'),

                                    Tables\Columns\TextColumn::make('created_by')
                                        ->label('')
                                        ->formatStateUsing(fn($state) => 'Admin')
                                        ->icon('heroicon-o-user-circle')
                                        ->iconColor('warning')
                                        ->size('xs')
                                        ->color('gray')
                                        ->prefix('👤 ')
                                        ->tooltip('Penulis'),

                                    Tables\Columns\TextColumn::make('view_count')
                                        ->label('')
                                        ->state(fn() => rand(50, 999) . ' views')
                                        ->icon('heroicon-o-eye')
                                        ->iconColor('success')
                                        ->size('xs')
                                        ->color('gray')
                                        ->prefix('👁️ ')
                                        ->tooltip('Total Views'),

                                    Tables\Columns\TextColumn::make('created_at')
                                        ->label('')
                                        ->since()
                                        ->icon('heroicon-o-clock')
                                        ->iconColor('purple')
                                        ->size('xs')
                                        ->color('gray')
                                        ->prefix('⏰ ')
                                        ->tooltip('Dibuat'),
                                ]),
                        ])->space(1),
                    ])->from('md'),
                ])
                    ->collapsible()
                    ->collapsed(false)
                    ->extraAttributes(['class' => 'mb-2']),                // Compact mobile view
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\ImageColumn::make('foto')
                                ->size(60)
                                ->circular()
                                ->defaultImageUrl('https://via.placeholder.com/120x120/e5e7eb/6b7280?text=📰')
                                ->extraAttributes(['class' => 'ring-2 ring-gray-200']),

                            Tables\Columns\Layout\Stack::make([
                                Tables\Columns\TextColumn::make('judul')
                                    ->weight('semibold')
                                    ->size('sm')
                                    ->limit(35)
                                    ->color('primary'),

                                Tables\Columns\TextColumn::make('tanggal')
                                    ->size('xs')
                                    ->color('gray')
                                    ->date('d/m/Y')
                                    ->prefix('📅 '),
                            ])->space(1),

                            Tables\Columns\TextColumn::make('status')
                                ->badge()
                                ->size('xs')
                                ->color(fn(string $state): string => match ($state) {
                                    'published' => 'success',
                                    'pending' => 'warning',
                                    'draft' => 'info',
                                    'archived' => 'danger',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn(string $state): string => match ($state) {
                                    'published' => '🚀',
                                    'pending' => '⏳',
                                    'draft' => '📝',
                                    'archived' => '📦',
                                    default => '❓',
                                }),
                        ]),
                    ])->space(1),
                ])
                    ->hiddenFrom('md'),
            ])
            ->contentGrid([
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('📊 Filter Status')
                    ->options([
                        'draft' => '📝 Draft',
                        'pending' => '⏳ Menunggu Review',
                        'published' => '🚀 Dipublikasikan',
                        'archived' => '📦 Diarsipkan',
                    ])
                    ->multiple()
                    ->preload()
                    ->indicator('Status'),

                Tables\Filters\Filter::make('tanggal')
                    ->label('📅 Filter Periode')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('dari_tanggal')
                                    ->label('Dari Tanggal')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->prefixIcon('heroicon-o-calendar'),
                                Forms\Components\DatePicker::make('sampai_tanggal')
                                    ->label('Sampai Tanggal')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->prefixIcon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicator('Periode'),

                Tables\Filters\Filter::make('popular')
                    ->label('⭐ Berita Populer')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->indicator('Populer'),
            ])
            ->actions([
                Tables\Actions\Action::make('quickView')
                    ->label('')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('info')
                    ->size('sm')
                    ->tooltip('👀 Quick Preview')
                    ->modalHeading(fn($record) => '📰 ' . $record->judul)
                    ->modalContent(function ($record) {
                        return view('filament.modals.berita-quick-preview', compact('record'));
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('👁️ Lihat Detail')
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->label('✏️ Edit Berita')
                        ->color('warning'),
                    Tables\Actions\Action::make('publish')
                        ->label('🚀 Publikasikan')
                        ->icon('heroicon-o-rocket-launch')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('🚀 Publikasikan Berita?')
                        ->modalDescription('Berita akan langsung tampil di website dan dapat dilihat oleh pengunjung.')
                        ->modalSubmitActionLabel('Ya, Publikasikan!')
                        ->action(function ($record) {
                            $record->update(['status' => 'published']);
                        })
                        ->visible(fn($record) => in_array($record->status, ['draft', 'pending'])),
                    Tables\Actions\Action::make('unpublish')
                        ->label('📝 Jadikan Draft')
                        ->icon('heroicon-o-document-text')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('📝 Ubah ke Draft?')
                        ->modalDescription('Berita akan disembunyikan dari website.')
                        ->action(fn($record) => $record->update(['status' => 'draft']))
                        ->visible(fn($record) => $record->status === 'published'),
                    Tables\Actions\Action::make('archive')
                        ->label('📦 Arsipkan')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('📦 Arsipkan Berita?')
                        ->modalDescription('Berita akan dipindahkan ke arsip dan tidak tampil di website.')
                        ->action(fn($record) => $record->update(['status' => 'archived']))
                        ->visible(fn($record) => $record->status === 'published'),
                    Tables\Actions\DeleteAction::make()
                        ->label('🗑️ Hapus')
                        ->color('danger')
                        ->modalHeading('🗑️ Hapus Berita?')
                        ->modalDescription('Data berita akan dihapus secara permanen dan tidak dapat dikembalikan.'),
                ])
                    ->label('⚡ Menu Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('primary')
                    ->button()
                    ->outlined(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('🚀 Publikasikan Terpilih')
                        ->icon('heroicon-o-rocket-launch')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('🚀 Publikasikan Berita Terpilih?')
                        ->modalDescription('Semua berita yang dipilih akan dipublikasikan.')
                        ->action(fn($records) => $records->each(fn($record) => $record->update(['status' => 'published']))),
                    Tables\Actions\BulkAction::make('archive')
                        ->label('📦 Arsipkan Terpilih')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('📦 Arsipkan Berita Terpilih?')
                        ->modalDescription('Semua berita yang dipilih akan diarsipkan.')
                        ->action(fn($records) => $records->each(fn($record) => $record->update(['status' => 'archived']))),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('🗑️ Hapus Terpilih')
                        ->requiresConfirmation()
                        ->modalHeading('🗑️ Hapus Berita Terpilih?')
                        ->modalDescription('Semua berita yang dipilih akan dihapus secara permanen.'),
                ])
                    ->label('📋 Aksi Massal'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('📝 Buat Berita Pertama')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary'),
            ])
            ->emptyStateHeading('📰 Belum Ada Berita')
            ->emptyStateDescription('Mulai membuat berita pertama untuk ditampilkan kepada pengunjung website.')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->defaultSort('tanggal', 'desc')
            ->striped(true)
            ->paginated([8, 16, 32, 64])
            ->poll('60s')
            ->deferLoading()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->searchOnBlur()
            ->filtersFormColumns(3)
            ->recordTitleAttribute('judul')
            ->recordClasses(fn($record) => match ($record->status) {
                'published' => 'bg-green-50 border-l-4 border-green-400',
                'pending' => 'bg-yellow-50 border-l-4 border-yellow-400',
                'draft' => 'bg-blue-50 border-l-4 border-blue-400',
                'archived' => 'bg-red-50 border-l-4 border-red-400',
                default => 'bg-gray-50',
            });
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
            'index' => Pages\ListTbBeritas::route('/'),
            'create' => Pages\CreateTbBerita::route('/create'),
            'view' => Pages\ViewTbBerita::route('/{record}'),
            'edit' => Pages\EditTbBerita::route('/{record}/edit'),
        ];
    }
}
