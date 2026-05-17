<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => Str::headline($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(12),
            'color' => $this->faker->randomElement(['#D4943A', '#2B8A7E', '#5F6F94', '#B85C38', '#6A7D39']),
            'icon' => $this->faker->randomElement(['fa-solid fa-code', 'fa-solid fa-server', 'fa-solid fa-cloud', 'fa-solid fa-chart-line', 'fa-solid fa-terminal']),
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }
}
