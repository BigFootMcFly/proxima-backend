<?php

/*
 * Endpoint: DELETE /discord-users/{discord_user}
 */

use App\Models\DiscordUser;
use App\Models\Remainder;
use App\Models\User;

beforeEach(function () {
    $this->endpoint = 'api/v1/discord-users/{discord_user}';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-users'])->plainTextToken;
    $this->discord_user = DiscordUser::factory()->create();
});

//---------------------------------------------------------------------------------------------------------------------
test('delete discord user succesfull', function () {
    deleteJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]),
        data: [
            'snowflake' => $this->discord_user->snowflake,
        ]
    )->assertStatus(204);
});

//---------------------------------------------------------------------------------------------------------------------
test('delete discord user with remainders forced succesfull', function () {
    Remainder::create([
        'discord_user_id' => $this->discord_user->id,
        'due_at' => fake()->dateTimeBetween('now', '+1 day'),
        'message' => 'There is no spoon...',
    ]);

    deleteJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]),
        data: [
            'snowflake' => $this->discord_user->snowflake,
        ]
    )->assertStatus(204);
});
