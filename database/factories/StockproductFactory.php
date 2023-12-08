<?php

namespace Database\Factories;

use App\Models\Stockproduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockproductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stockproduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'ivaaliquot_id' => rand(1, 5),
            'costo' => $this->faker->randomFloat(4, 0, 10000),
            'stockproductgroup_id' => $this->faker->boolean(50) ? rand(1, 30) : null
        ];
    }
}
