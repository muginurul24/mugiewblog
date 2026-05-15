<?php

namespace Database\Factories;

use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => Article::factory()->published(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'guest_name' => null,
            'guest_email' => null,
            'content' => $this->faker->paragraph(),
            'status' => CommentStatus::Approved,
            'approved_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CommentStatus::Pending,
            'approved_at' => null,
        ]);
    }
}
