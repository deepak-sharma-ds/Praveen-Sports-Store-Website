<?php

namespace Webkul\Product360\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product360\Models\Product360Image;
use Webkul\Product\Models\Product;

class Product360ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product360Image::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'product-360-images/' . $this->faker->uuid . '/' . $this->faker->uuid . '.jpg',
            'position' => $this->faker->numberBetween(1, 100),
        ];
    }
}
