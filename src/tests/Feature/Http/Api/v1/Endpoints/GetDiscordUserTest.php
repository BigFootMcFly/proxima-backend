<?php

/*
 * Endpoint: GET /discord-users/{discord_user}
 */

use App\Http\Resources\Api\v1\DiscordUserResource;
use App\Models\DiscordUser;
use App\Models\User;

beforeEach(function () {
    $this->endpoint = '/api/v1/discord-users/{discord_user}';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-users'])->plainTextToken;
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list endpoint requires authorization', function () {
    $this->getJson(endpoint())
        ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('get discord user succesfull', function () {

    $discord_user = DiscordUser::factory()->create();
    $responseData = json_decode((DiscordUserResource::make($discord_user))->toJson(), true);

    getJsonAuthorized(endpoint(parameters: ['discord_user' => $discord_user->id]))
        ->assertStatus(200)
        ->assertJsonPath('data', $responseData);
});
