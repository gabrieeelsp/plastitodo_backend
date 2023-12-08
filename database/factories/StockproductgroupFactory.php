<?php

namespace Database\Factories;

use App\Models\Stockproductgroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockproductgroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stockproductgroup::class;

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
