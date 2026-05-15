<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->author(),
            'filename' => fake()->uuid().'.webp',
            'original_name' => fake()->slug().'.jpg',
            'path' => 'articles/'.fake()->uuid().'.webp',
            'mime_type' => 'image/webp',
            'size' => fake()->numberBetween(50_000, 900_000),
            'alt_text' => fake()->sentence(6),
            'folder' => fake()->randomElement(['articles', 'avatars', 'general']),
        ];
    }
}
