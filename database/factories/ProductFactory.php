<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'price' => $this->faker->numberBetween(100, 1000),
            'code' => $this->faker->unique()->word(),
            'stock' => $this->faker->numberBetween(0, 100),
            'min_stock' => $this->faker->numberBetween(10, 100),
            'notes' => $this->faker->text(),
            'image' => $this->faker->imageUrl(),
            'brand_id' => BrandFactory::new(),
            'unit_id' => UnitFactory::new(),
        ];
    }
}
