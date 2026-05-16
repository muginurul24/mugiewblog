<x-filament-panels::page>
    @php($stats = $this->overviewStats())
    @php($recentSubscribers = $this->recentSubscribers())

    <div class="admin-overview-grid">
        <section class="admin-stat-card">
            <span>Total subscriber</span>
            <strong>{{ number_format($stats['total']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Aktif</span>
            <strong>{{ number_format($stats['subscribed']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Menunggu</span>
            <strong>{{ number_format($stats['pending']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Minggu ini</span>
            <strong>+{{ number_format($stats['this_week']) }}</strong>
        </section>
    </div>

    <div class="admin-split-grid">
        <section class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Status subscriber</h2>
                    <p>Distribusi kesehatan daftar newsletter saat ini.</p>
                </div>
            </div>
            <div class="admin-segment-list">
                <div>
                    <span>Aktif</span>
                    <strong>{{ $stats['subscribed'] }}</strong>
                </div>
                <div>
                    <span>Menunggu verifikasi</span>
                    <strong>{{ $stats['pending'] }}</strong>
                </div>
                <div>
                    <span>Berhenti</span>
                    <strong>{{ $stats['unsubscribed'] }}</strong>
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Subscriber terbaru</h2>
                    <p>Entri paling baru dari funnel publik.</p>
                </div>
            </div>
            <div class="admin-list-stack">
                @forelse ($recentSubscribers as $subscriber)
                    <div>
                        <strong>{{ $subscriber->email }}</strong>
                        <span>{{ $subscriber->created_at?->diffForHumans() }} · {{ ucfirst($subscriber->status) }}</span>
                    </div>
                @empty
                    <p class="admin-empty-copy">Belum ada subscriber.</p>
                @endforelse
            </div>
        </section>
    </div>

    {{ $this->content }}
</x-filament-panels::page>
