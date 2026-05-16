<x-filament-panels::page>
    @php($stats = $this->overviewStats())
    @php($categories = $this->overviewCategories())

    <div class="admin-overview-grid">
        <section class="admin-stat-card">
            <span>Total kategori</span>
            <strong>{{ $stats['total'] }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Kategori induk</span>
            <strong>{{ $stats['roots'] }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Artikel tertaut</span>
            <strong>{{ $stats['linked_articles'] }}</strong>
        </section>
    </div>

    <section class="admin-panel">
        <div class="admin-panel-heading">
            <div>
                <h2>Peta kategori</h2>
                <p>Warna, hierarki, dan beban konten terlihat sebelum mengelola tabel detail.</p>
            </div>
        </div>

        <div class="admin-card-grid">
            @forelse ($categories as $category)
                <article class="admin-category-card">
                    <div class="flex items-start justify-between gap-4">
                        <span class="admin-category-swatch" style="background-color: {{ $category->color }}"></span>
                        <span class="admin-chip">{{ $category->articles_count }} artikel</span>
                    </div>
                    <h3>{{ $category->name }}</h3>
                    <p>{{ $category->description ?: 'Belum ada deskripsi kategori.' }}</p>
                    <dl>
                        <div>
                            <dt>Slug</dt>
                            <dd>{{ $category->slug }}</dd>
                        </div>
                        <div>
                            <dt>Induk</dt>
                            <dd>{{ $category->parent?->name ?? 'Utama' }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <p class="admin-empty-copy">Belum ada kategori.</p>
            @endforelse
        </div>
    </section>

    {{ $this->content }}
</x-filament-panels::page>
