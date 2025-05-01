<?php

/*
 * Endpoint: PUT /discord-users/{discord_user}
 */

use App\Models\DiscordUser;
use App\Models\User;

beforeEach(function () {
    $this->endpoint = '/api/v1/discord-users/{discord_user}';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-users'])->plainTextToken;
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list endpoint requires authorization', function () {
    $this->putJson(endpoint())
        ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('update discord user succesfull', function () {
    $discord_user = DiscordUser::factory()->create();
    $newName = 'Biggus Dickus';

    putJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $discord_user->id]),
        data: [
            'snowflake' => $discord_user->snowflake,
            'user_name' => $newName,
        ]
    )->assertStatus(200);

    $this->assertEquals(
        (DiscordUser::find($discord_user->id))->user_name,
        $newName
    );
});
