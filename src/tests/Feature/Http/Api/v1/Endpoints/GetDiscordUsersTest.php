<?php

/*
 * Endpoint: GET /discord-users/
 */

use App\Http\Resources\Api\v1\DiscordUserResource;
use App\Models\DiscordUser;
use App\Models\User;

beforeEach(function () {
    $this->endpoint = '/api/v1/discord-users/';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-users'])->plainTextToken;
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list endpoint requires authorization', function () {
    $this->getJson(endpoint())
        ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    getJsonAuthorized(headers: ['Authorization' => 'Bearer '.$badToken])
        ->assertStatus(403);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list endpoint accepts good token', function () {
    getJsonAuthorized()
        ->assertStatus(200);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list is empty on empty database', function () {
    getJsonAuthorized()
        ->assertJsonCount(0, 'data');
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list yields the correct result', function () {
    $user = DiscordUser::factory()->create();
    $responseData = [
        json_decode((DiscordUserResource::make($user))->toJson(), true),
    ];

    getJsonAuthorized()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data', $responseData);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user list pagination is sanitized', function () {
    getJsonAuthorized()
        ->assertJsonMissingPath('links')
        ->assertJsonMissingPath('meta.links')
        ->assertJsonMissingPath('meta.path')
    ;
});
