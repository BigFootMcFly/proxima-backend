<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscordUser>
 */
class DiscordUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $globalName = $firstName.' '.$lastName;
        $userName = mb_strtolower($firstName.'.'.$lastName.'.'.random_int(11, 99));
        return [
            'snowflake' => $this->faker->numerify(str_repeat('#', 18)),
            'user_name' => $userName,
            'global_name' => $globalName,
            'locale' => $this->faker->randomElement(LOCALES),
            'timezone' => $this->faker->timezone(),
        ];
    }
}
