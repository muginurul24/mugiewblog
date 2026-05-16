<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Throwable;

class CacheQueue extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'Cache & Queue';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected string $view = 'filament.pages.cache-queue';

    public function getTitle(): string|Htmlable
    {
        return 'Cache & Queue';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'cache';
    }

    /**
     * @return array{
     *     status: string,
     *     recent_jobs: int,
     *     failed_jobs: int,
     *     jobs_per_minute: int,
     *     throughput: int
     * }
     */
    #[\NoDiscard]
    public function overviewStats(): array
    {
        try {
            return [
                'status' => app(MasterSupervisorRepository::class)->all() === [] ? 'inactive' : 'running',
                'recent_jobs' => app(JobRepository::class)->countRecent(),
                'failed_jobs' => app(JobRepository::class)->countRecentlyFailed(),
                'jobs_per_minute' => app(MetricsRepository::class)->jobsProcessedPerMinute(),
                'throughput' => app(MetricsRepository::class)->throughput(),
            ];
        } catch (Throwable) {
            return [
                'status' => 'unavailable',
                'recent_jobs' => 0,
                'failed_jobs' => 0,
                'jobs_per_minute' => 0,
                'throughput' => 0,
            ];
        }
    }

    /**
     * @return array<int, array{"name": string, "length": int, "wait": int, "processes": int, "split_queues": null|array<int, array{"name": string, "wait": int, "length": int}>}>
     */
    #[\NoDiscard]
    public function workload(): array
    {
        try {
            return app(WorkloadRepository::class)->get();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<int, object>
     */
    #[\NoDiscard]
    public function recentJobs(): array
    {
        try {
            return app(JobRepository::class)
                ->getRecent()
                ->take(8)
                ->values()
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('horizon')
                ->label('Buka Horizon')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(url('/horizon'))
                ->openUrlInNewTab(),
        ];
    }

    public function clearApplicationCache(): void
    {
        $this->runMaintenanceAction(
            fn (): int => Artisan::call('cache:clear'),
            'Cache aplikasi dibersihkan.',
        );
    }

    public function clearViewCache(): void
    {
        $this->runMaintenanceAction(
            fn (): int => Artisan::call('view:clear'),
            'Cache view dibersihkan.',
        );
    }

    public function clearRouteCache(): void
    {
        $this->runMaintenanceAction(
            fn (): int => Artisan::call('route:clear'),
            'Cache route dibersihkan.',
        );
    }

    public function clearConfigCache(): void
    {
        $this->runMaintenanceAction(
            fn (): int => Artisan::call('config:clear'),
            'Cache config dibersihkan.',
        );
    }

    public function clearHorizonMetrics(): void
    {
        $this->runMaintenanceAction(
            fn (): int => Artisan::call('horizon:clear-metrics'),
            'Metric Horizon dibersihkan.',
        );
    }

    private function notifyAction(string $message): void
    {
        Notification::make()
            ->success()
            ->title($message)
            ->send();
    }

    private function notifyFailure(): void
    {
        Notification::make()
            ->danger()
            ->title('Aksi gagal dijalankan')
            ->body('Periksa koneksi Redis dan konfigurasi runtime.')
            ->send();
    }

    private function runMaintenanceAction(callable $callback, string $successMessage): void
    {
        try {
            $callback();
            $this->notifyAction($successMessage);
        } catch (Throwable) {
            $this->notifyFailure();
        }
    }
}
