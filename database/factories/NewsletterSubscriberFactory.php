<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsletterSubscriber>
 */
class NewsletterSubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'status' => 'subscribed',
            'source' => $this->faker->randomElement(['footer', 'article', 'homepage']),
            'verification_token' => Str::random(48),
            'verified_at' => now(),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ];
    }
}
