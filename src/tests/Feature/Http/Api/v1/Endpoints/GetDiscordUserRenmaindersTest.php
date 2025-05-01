<?php

/*
 * Endpoint: GET /discord-users/
 */

use App\Http\Resources\Api\v1\RemainderResource;
use App\Models\DiscordUser;
use App\Models\Remainder;
use App\Models\User;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->endpoint = '/api/v1/discord-users/{discord_user}/remainders';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-user-remainders'])->plainTextToken;
    $this->discord_user = DiscordUser::factory()->create();
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder list endpoint requires authorization', function () {
    $this->getJson(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id])
    )->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder list endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    getJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]),
        headers: ['Authorization' => 'Bearer '.$badToken]
    )->assertStatus(403);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder list endpoint accepts good token', function () {
    getJsonAuthorized(uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]))
        ->assertStatus(200);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder list is empty on empty database', function () {
    getJsonAuthorized(uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]))
        ->assertJsonFragment(['data' => []])
    ;
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder list yields the correct result', function () {
    Remainder::create([
        'discord_user_id' => $this->discord_user->id,
        'due_at' => fake()->dateTimeBetween('now', '+1 day'),
        'message' => 'There is no spoon...',
    ]);

    getJsonAuthorized(uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]))
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['data' => RemainderResource::collection($this->discord_user->remainders)->toArray(new Request())])
    ;
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder list pagination is sanitized', function () {
    $x = Remainder::create([
        'discord_user_id' => $this->discord_user->id,
        'due_at' => fake()->dateTimeBetween('now', '+1 day'),
        'message' => 'There is no spoon...',
    ]);
    getJsonAuthorized(uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]))
        ->assertJsonMissingPath('links')
        ->assertJsonMissingPath('meta.links')
        ->assertJsonMissingPath('meta.path')
    ;
});
