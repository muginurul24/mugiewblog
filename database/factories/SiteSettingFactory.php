<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_name' => fake()->company(),
            'tagline' => fake()->sentence(),
            'site_url' => fake()->url(),
            'site_description' => fake()->paragraph(),
            'default_og_image' => 'settings/og/default.webp',
            'contact_email' => fake()->safeEmail(),
            'sitemap_enabled' => true,
            'rss_enabled' => true,
            'newsletter_enabled' => true,
            'articles_per_page' => 11,
        ];
    }
}
