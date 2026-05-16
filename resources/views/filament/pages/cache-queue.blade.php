<x-filament-panels::page>
    @php($stats = $this->overviewStats())
    @php($workload = $this->workload())
    @php($recentJobs = $this->recentJobs())

    <div class="admin-overview-grid">
        <section class="admin-stat-card">
            <span>Status Horizon</span>
            <strong>{{ ucfirst($stats['status']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Jobs terbaru</span>
            <strong>{{ number_format($stats['recent_jobs']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Gagal terbaru</span>
            <strong>{{ number_format($stats['failed_jobs']) }}</strong>
        </section>
        <section class="admin-stat-card">
            <span>Jobs / menit</span>
            <strong>{{ number_format($stats['jobs_per_minute']) }}</strong>
        </section>
    </div>

    <div class="admin-split-grid">
        <section class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Cache management</h2>
                    <p>Aksi operasional dengan umpan balik langsung.</p>
                </div>
            </div>
            <div class="admin-action-grid">
                <button type="button" wire:click="clearViewCache">Flush view cache</button>
                <button type="button" wire:click="clearRouteCache">Flush route cache</button>
                <button type="button" wire:click="clearConfigCache">Flush config cache</button>
                <button type="button" wire:click="clearApplicationCache">Flush app cache</button>
                <button type="button" wire:click="clearHorizonMetrics">Clear Horizon metrics</button>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Workload queue</h2>
                    <p>Snapshot koneksi Redis dan antrian Horizon.</p>
                </div>
            </div>
            <div class="admin-list-stack">
                @forelse ($workload as $queue)
                    <div>
                        <strong>{{ $queue['name'] }}</strong>
                        <span>{{ $queue['length'] }} pending · {{ $queue['processes'] }} worker · wait {{ $queue['wait'] }}s</span>
                    </div>
                @empty
                    <p class="admin-empty-copy">Belum ada workload aktif.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="admin-panel">
        <div class="admin-panel-heading">
            <div>
                <h2>Jobs terbaru</h2>
                <p>Riwayat kerja yang ditangkap Horizon.</p>
            </div>
        </div>
        <div class="admin-job-list">
            @forelse ($recentJobs as $job)
                <article>
                    <strong>{{ $job->name ?? 'Unknown job' }}</strong>
                    <span>{{ $job->queue ?? '-' }} · {{ $job->status ?? '-' }}</span>
                </article>
            @empty
                <p class="admin-empty-copy">Belum ada job terbaru.</p>
            @endforelse
        </div>
    </section>
</x-filament-panels::page>
