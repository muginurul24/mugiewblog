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
            'filename' => $this->faker->uuid().'.webp',
            'original_name' => $this->faker->slug().'.jpg',
            'path' => 'articles/'.$this->faker->uuid().'.webp',
            'mime_type' => 'image/webp',
            'size' => $this->faker->numberBetween(50_000, 900_000),
            'alt_text' => $this->faker->sentence(6),
            'folder' => $this->faker->randomElement(['articles', 'avatars', 'general']),
        ];
    }
}
