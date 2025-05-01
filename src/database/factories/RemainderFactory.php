<?php

namespace Database\Factories;

use App\Enums\RemainderStatus;
use App\Models\DiscordUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Lottery;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Remainder>
 */
class RemainderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $channel_id = match (Lottery::odds(25, 100)->choose()) {
            true => $this->faker->numerify(str_repeat('#', 18)),
            false => null
        };

        return [
            'discord_user_id' => DiscordUser::factory(),
            'channel_id' => $channel_id,
            'due_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'message' => $this->faker->realText(32),
            'status' => RemainderStatus::NEW->value,
        ];
    }
}
