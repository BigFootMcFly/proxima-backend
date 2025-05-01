<?php

/*
 * Endpoint: POST /discord-users/{discord_user}/remainders
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
test('discord user remainder create endpoint requires authorization', function () {
    $this->postJson(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id])
    )
    ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder create endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    postJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]),
        headers: ['Authorization' => 'Bearer '.$badToken]
    )->assertStatus(403);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder create endpoint validates parameters', function () {
    postJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]),
        data: [],
    )

    //NOTE: this is here to assure, that in case other required fields are added, the test will be updated as well
    ->assertJsonCount(2, 'errors')

    ->assertJsonValidationErrors([
        'due_at' => 'required',
        'message' => 'required',
    ])
    ->assertStatus(422)
    ;
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder create yields the correct result', function () {
    $remainder = Remainder::factory()->make([
        'discord_user_id' => $this->discord_user->id,
        'id' => 1,
    ]);

    postJsonAuthorized(
        uri: endpoint(parameters: ['discord_user' => $this->discord_user->id]),
        data: [
            'snowflake' => $this->discord_user->snowflake,
            'due_at' => $remainder->due_at,
            'message' => $remainder->message,
            'channel_id' => $remainder->channel_id,
        ]
    )
        ->assertExactJson(['data' => RemainderResource::make($remainder)->toArray(new Request())])
        ->assertStatus(201)
    ;
});
