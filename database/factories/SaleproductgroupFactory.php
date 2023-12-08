<?php

namespace Database\Factories;

use App\Models\Saleproductgroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleproductgroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Saleproductgroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
