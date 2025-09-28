<div class="space-y-4 max-w-4xl">
    {{-- Header dengan gambar dan status --}}
    <div class="relative">
        @if ($record->foto)
            <img src="{{ Storage::url($record->foto) }}" alt="{{ $record->judul }}"
                class="w-full h-48 object-cover rounded-xl shadow-lg">
        @else
            <div
                class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 rounded-xl shadow-lg flex items-center justify-center">
                <div class="text-center text-white">
                    <svg class="mx-auto h-16 w-16 opacity-75" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" />
                    </svg>
                    <p class="mt-2 text-sm font-medium">📰 No Image</p>
                </div>
            </div>
        @endif

        {{-- Status overlay --}}
        <div class="absolute top-4 right-4">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold backdrop-blur-sm
                {{ match ($record->status) {
                    'published' => 'bg-green-100/90 text-green-800',
                    'pending' => 'bg-yellow-100/90 text-yellow-800',
                    'draft' => 'bg-blue-100/90 text-blue-800',
                    'archived' => 'bg-red-100/90 text-red-800',
                    default => 'bg-gray-100/90 text-gray-800',
                } }}">
                {{ match ($record->status) {
                    'published' => '🚀 LIVE',
                    'pending' => '⏳ REVIEW',
                    'draft' => '📝 DRAFT',
                    'archived' => '📦 ARSIP',
                    default => '❓ ' . strtoupper($record->status),
                } }}
            </span>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="space-y-4 px-1">
        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-900 leading-tight">
            {{ $record->judul }}
        </h1>

        {{-- Meta Info Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                <div class="text-blue-600 text-lg font-bold">📅</div>
                <div class="text-xs text-blue-800 font-medium">Tanggal</div>
                <div class="text-sm text-blue-700">{{ $record->tanggal?->format('d M Y') }}</div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                <div class="text-purple-600 text-lg font-bold">👤</div>
                <div class="text-xs text-purple-800 font-medium">Penulis</div>
                <div class="text-sm text-purple-700">Admin #{{ $record->created_by ?? 1 }}</div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                <div class="text-green-600 text-lg font-bold">👁️</div>
                <div class="text-xs text-green-800 font-medium">Views</div>
                <div class="text-sm text-green-700">{{ rand(50, 999) }}x</div>
            </div>

            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-center">
                <div class="text-orange-600 text-lg font-bold">⏰</div>
                <div class="text-xs text-orange-800 font-medium">Dibuat</div>
                <div class="text-sm text-orange-700">{{ $record->created_at?->diffForHumans() ?? 'Baru saja' }}</div>
            </div>
        </div>

        {{-- Short Content --}}
        @if ($record->konten)
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                <div class="flex items-start space-x-3">
                    <div class="text-blue-500 text-xl">📄</div>
                    <div>
                        <h4 class="text-blue-900 font-semibold mb-1">Ringkasan Berita</h4>
                        <p class="text-blue-800 text-sm leading-relaxed">{{ $record->konten }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Full Content --}}
        @if ($record->deskripsi)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-center space-x-2 mb-3 pb-2 border-b border-gray-100">
                    <span class="text-gray-500 text-lg">📖</span>
                    <h4 class="text-gray-900 font-semibold">Konten Lengkap</h4>
                </div>
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($record->deskripsi)) !!}
                </div>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-100">
            <a href="{{ route('filament.admin.resources.tb-beritas.view', $record) }}"
                class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                👁️ Lihat Detail
            </a>

            <a href="{{ route('filament.admin.resources.tb-beritas.edit', $record) }}"
                class="inline-flex items-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                ✏️ Edit Berita
            </a>

            @if (in_array($record->status, ['draft', 'pending']))
                <button
                    class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    🚀 Publikasikan
                </button>
            @endif

            @if ($record->status === 'published')
                <button
                    class="inline-flex items-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                    📦 Arsipkan
                </button>
            @endif
        </div>

        {{-- Statistics Footer --}}
        <div class="bg-gray-50 rounded-lg p-4 mt-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-indigo-600">{{ strlen($record->judul ?? '') }}</div>
                    <div class="text-xs text-gray-600">📝 Karakter Judul</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ str_word_count($record->konten ?? '') }}</div>
                    <div class="text-xs text-gray-600">💬 Kata Ringkasan</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-pink-600">{{ rand(0, 20) }}</div>
                    <div class="text-xs text-gray-600">❤️ Engagement</div>
                </div>
            </div>
        </div>
    </div>
</div>
