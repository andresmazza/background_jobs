<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomJob>
 */
class CustomJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pid' => fake()->uuid(),
            'priority' => 0,
            'status' => 3,
            'payload' => 'O:8:"stdClass":5:{s:5:"class";s:19:"App\Jobs\ExampleJob";s:6:"method";s:6:"handle";s:6:"params";a:1:{i:0;a:1:{i:0;s:1:"2";}}s:10:"maxRetries";i:3;s:10:"retryDelay";i:60;}',
            'attempts' => 1,
            'description' => fake()->sentence(),
            'finished_at' => now()
        ];
    }
}
