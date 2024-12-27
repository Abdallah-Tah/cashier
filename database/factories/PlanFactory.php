<?php

namespace Database\Factories;

use Illuminate\Support\Facades\DB;
use AMohamed\OfflineCashier\Models\Plan;
use AMohamed\OfflineCashier\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AMohamed\OfflineCashier\Models\Plan>
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
            'stripe_price_id' => $this->faker->optional()->uuid,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Plan $plan) {
            DB::table('feature_plan')->where('plan_id', $plan->id)->delete();
            $features = Feature::factory()->count(3)->create();
            $plan->features()->sync($features->pluck('id'));
        });
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

    /**
     * Add specific features to the plan.
     */
    public function withFeatures(array $features): self
    {
        return $this->afterCreating(function (Plan $plan) use ($features) {
            $plan->features()->sync($features);
        });
    }
}
