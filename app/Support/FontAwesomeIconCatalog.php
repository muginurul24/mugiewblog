<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FontAwesomeIconCatalog
{
    /**
     * @var array<string, array{prefix: string, label: string}>
     */
    private const STYLES = [
        'solid' => [
            'prefix' => 'fa-solid',
            'label' => 'Solid',
        ],
        'regular' => [
            'prefix' => 'fa-regular',
            'label' => 'Regular',
        ],
        'brands' => [
            'prefix' => 'fa-brands',
            'label' => 'Brands',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const STYLE_ALIASES = [
        'fas' => 'fa-solid',
        'far' => 'fa-regular',
        'fab' => 'fa-brands',
    ];

    /**
     * @var array<int, string>
     */
    private const RECOMMENDED = [
        'fa-solid fa-folder',
        'fa-solid fa-code',
        'fa-solid fa-cloud',
        'fa-solid fa-server',
        'fa-solid fa-terminal',
        'fa-solid fa-microchip',
        'fa-solid fa-chart-line',
        'fa-solid fa-database',
        'fa-solid fa-shield-halved',
        'fa-solid fa-rocket',
        'fa-regular fa-newspaper',
        'fa-regular fa-folder',
        'fa-regular fa-comment',
        'fa-brands fa-github',
        'fa-brands fa-laravel',
        'fa-brands fa-php',
        'fa-brands fa-golang',
        'fa-brands fa-rust',
        'fa-brands fa-docker',
        'fa-brands fa-bitcoin',
    ];

    private function __construct() {}

    /**
     * @return array<string, string>
     */
    public static function recommendedOptions(): array
    {
        return self::optionsFor(self::RECOMMENDED);
    }

    /**
     * @return array<string, string>
     */
    public static function search(?string $search, int $limit = 50): array
    {
        $term = Str::of($search ?? '')->lower()->squish()->toString();

        return self::icons()
            ->filter(function (array $icon) use ($term): bool {
                if ($term === '') {
                    return true;
                }

                return str_contains($icon['search'], $term);
            })
            ->take($limit)
            ->mapWithKeys(fn (array $icon): array => [
                $icon['value'] => self::optionLabel($icon['value']),
            ])
            ->all();
    }

    public static function optionLabel(?string $value): ?string
    {
        $value = self::normalizeValue($value);

        $icon = self::icons()->firstWhere('value', $value);

        if ($icon === null) {
            return null;
        }

        return self::renderLabel(
            value: $icon['value'],
            label: $icon['label'],
            style: $icon['style'],
        );
    }

    public static function normalizeValue(?string $value): string
    {
        $parts = preg_split('/\s+/', trim((string) $value)) ?: [];
        $style = 'fa-solid';
        $icon = null;

        foreach ($parts as $part) {
            $part = self::STYLE_ALIASES[$part] ?? $part;

            if (in_array($part, array_column(self::STYLES, 'prefix'), true)) {
                $style = $part;

                continue;
            }

            if (str_starts_with($part, 'fa-')) {
                $icon = $part;
            }
        }

        return $style.' '.($icon ?: 'fa-folder');
    }

    public static function contains(?string $value): bool
    {
        return in_array(self::normalizeValue($value), self::values(), true);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return self::icons()
            ->pluck('value')
            ->all();
    }

    /**
     * @return Collection<int, array{value: string, label: string, style: string, search: string}>
     */
    private static function icons(): Collection
    {
        return once(fn (): Collection => collect(self::STYLES)
            ->flatMap(function (array $style, string $directory): Collection {
                $path = resource_path("fontawesome/svgs/{$directory}");

                if (! File::isDirectory($path)) {
                    return collect();
                }

                return collect(File::files($path))
                    ->filter(fn (\SplFileInfo $file): bool => $file->getExtension() === 'svg')
                    ->map(function (\SplFileInfo $file) use ($style): array {
                        $name = $file->getBasename('.svg');
                        $label = Str::of($name)->replace('-', ' ')->title()->toString();
                        $value = "{$style['prefix']} fa-{$name}";

                        return [
                            'value' => $value,
                            'label' => $label,
                            'style' => $style['label'],
                            'search' => Str::of("{$label} {$name} {$style['label']} {$value}")
                                ->lower()
                                ->toString(),
                        ];
                    });
            })
            ->sortBy([
                ['style', 'asc'],
                ['label', 'asc'],
            ])
            ->values());
    }

    /**
     * @param  array<int, string>  $values
     * @return array<string, string>
     */
    private static function optionsFor(array $values): array
    {
        return collect($values)
            ->mapWithKeys(function (string $value): array {
                $label = self::optionLabel($value);

                return $label === null ? [] : [$value => $label];
            })
            ->all();
    }

    private static function renderLabel(string $value, string $label, string $style): string
    {
        $value = e($value);
        $label = e($label);
        $style = e($style);

        return <<<HTML
            <span style="display: flex; align-items: center; gap: .5rem;">
                <i class="{$value}" style="width: 1.25rem; text-align: center;" aria-hidden="true"></i>
                <span>{$label}</span>
                <span style="margin-left: auto; font-size: .75rem; color: rgb(107 114 128);">{$style}</span>
            </span>
        HTML;
    }
}
