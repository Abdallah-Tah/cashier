<?php

namespace Database\Factories;

use AMohamed\OfflineCashier\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'billing_interval' => $this->faker->randomElement(['month', 'year']),
            'trial_period_days' => $this->faker->optional()->numberBetween(7, 30),
            'features' => ['feature1', 'feature2', 'feature3'],
            'stripe_price_id' => $this->faker->optional()->uuid,
        ];
    }

    public function withTrial(int $days = 14): self
    {
        return $this->state([
            'trial_period_days' => $days,
        ]);
    }

    public function monthly(float $price = 9.99): self
    {
        return $this->state([
            'billing_interval' => 'month',
            'price' => $price,
        ]);
    }

    public function yearly(float $price = 99.99): self
    {
        return $this->state([
            'billing_interval' => 'year',
            'price' => $price,
        ]);
    }
}
