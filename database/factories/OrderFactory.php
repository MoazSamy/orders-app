<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User; // Assuming User is the delivery personnel
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_name' => $this->faker->name(),
            'delivery_address' => $this->faker->address(),
            'order_total' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}