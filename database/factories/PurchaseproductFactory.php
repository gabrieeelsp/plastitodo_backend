<?php

namespace Database\Factories;

use App\Models\Purchaseproduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseproductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchaseproduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'stockproduct_id' => rand(1, 1000),
            'supplier_id' => rand(1, 19),
            'name' => $this->faker->name(),
            'relacion_compra_stock' => $this->faker->randomElement([1, 1, 1, 1, 1, 10, 0.1]),
        ];
    }
}
