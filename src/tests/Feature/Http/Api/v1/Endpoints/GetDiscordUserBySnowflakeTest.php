<?php

/*
 * Endpoint: GET /discord-users/{discord_user}
 */

use App\Http\Resources\Api\v1\DiscordUserResource;
use App\Models\DiscordUser;
use App\Models\User;

beforeEach(function () {
    $this->endpoint = '/api/v1/discord-user-by-snowflake/{discord_user}';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-user-by-snowflake'])->plainTextToken;
    $this->discord_user = DiscordUser::factory()->create();
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user by snowflake endpoint requires authorization', function () {
    $this->getJson(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->snowflake])
    )->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user by snowflake endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    getJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->snowflake]),
        headers: ['Authorization' => 'Bearer '.$badToken]
    )->assertStatus(403);
});


//---------------------------------------------------------------------------------------------------------------------
test('get discord user by snowflake succesfull', function () {

    $discord_user = DiscordUser::factory()->create();
    $responseData = json_decode((DiscordUserResource::make($discord_user))->toJson(), true);

    getJsonAuthorized(endpoint(parameters: ['discord_user' => $discord_user->snowflake]))
        ->assertStatus(200)
        ->assertJsonPath('data', $responseData);
});
