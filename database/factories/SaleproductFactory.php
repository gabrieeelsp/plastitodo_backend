<?php

namespace Database\Factories;

use App\Models\Saleproduct;
use App\Models\Stockproduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleproductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Saleproduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'stockproduct_id' => rand(1, 1000),
            'name' => $this->faker->name(),
            'relacion_venta_stock' => $this->faker->randomElement([1, 1, 1, 1, 1, 10, 0.1]),
            'porc_min' => $this->faker->randomElement([40, 30, 20]),
            'porc_may' => $this->faker->randomElement([15, 10, 20]),

            'precision_min' => $this->faker->randomElement([-1, 0, 1]),
            'precision_may' => $this->faker->randomElement([-1, 0, 2]),

            'saleproductgroup_id' => $this->faker->boolean(50) ? rand(1, 30) : null
        ];
    }
}
