<x-filament-panels::page>
    @php($stats = $this->overviewStats())
    @php($recentMedia = $this->recentMedia())

    <div class="admin-overview-grid">
        <section class="admin-stat-card">
            <span>Total file</span>
            <strong>{{ number_format($stats['files']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Total storage</span>
            <strong>{{ number_format($stats['bytes'] / 1024 / 1024, 1) }} MB</strong>
        </section>
        <section class="admin-stat-card">
            <span>Folder</span>
            <strong>{{ $stats['folders'] }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Gambar</span>
            <strong>{{ $stats['images'] }}</strong>
        </section>
    </div>

    <section class="admin-panel">
        <div class="admin-panel-heading">
            <div>
                <h2>Media terbaru</h2>
                <p>Pratinjau cepat sebelum masuk ke pencarian dan filter tabel penuh.</p>
            </div>
        </div>

        <div class="admin-media-grid">
            @forelse ($recentMedia as $media)
                <article class="admin-media-card">
                    <img src="{{ $media->url() }}" alt="{{ $media->alt_text ?: $media->original_name }}">
                    <div>
                        <strong>{{ $media->original_name }}</strong>
                        <span>{{ $media->folder }} · {{ number_format($media->size / 1024, 1) }} KB</span>
                    </div>
                </article>
            @empty
                <p class="admin-empty-copy">Belum ada media.</p>
            @endforelse
        </div>
    </section>

    {{ $this->content }}
</x-filament-panels::page>
