<?php

/*
 * Endpoint: PUT /discord-users/{discord_user}/remainders/{remainder}
 */

use App\Http\Resources\Api\v1\RemainderResource;
use App\Models\DiscordUser;
use App\Models\Remainder;
use App\Models\User;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->endpoint = '/api/v1/discord-users/{discord_user}/remainders/{remainder}';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['discord-user-remainders'])->plainTextToken;
    $this->discord_user = DiscordUser::factory()->create();
    $this->remainder = $remainder = Remainder::factory()->create([
        'discord_user_id' => $this->discord_user->id,
    ]);
    $this->apiEndpoint = endpoint(parameters: [
        'discord_user' => $this->discord_user->id,
        'remainder' => $this->remainder->id,
    ]);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder update endpoint requires authorization', function () {

    $this->putJson(uri: $this->apiEndpoint)
        ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder update endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    putJsonAuthorized(
        uri: $this->apiEndpoint,
        headers: ['Authorization' => 'Bearer '.$badToken]
    )->assertStatus(403);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder update endpoint validates parameters', function () {

    // check for non-existing discord_user
    putJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id + 1,
            'remainder' => $this->remainder->id,
        ]),
    )
    ->assertStatus(404);

    // check for non-existing remainder
    putJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id + 1,
        ]),
    )
    ->assertStatus(404);

    // check for remainder not belonging to discord_user
    $secondUser = DiscordUser::factory()->create();
    putJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $secondUser->id,
            'remainder' => $this->remainder->id,
        ]),
    )
    ->assertJsonValidationErrorFor('remainder')
    ->assertStatus(422);

    // check for valid data
    putJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
    )
    ->assertStatus(200);

});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder update yields the correct result', function () {

    $updatedRemainder = Remainder::factory()->make([
        'id' => $this->remainder->id,
        'discord_user_id' => $this->discord_user->id,
    ]);

    $updatedRemainderData = RemainderResource::make($updatedRemainder)->toArray(new Request());

    $neededResponseJson = [
        'data' => $updatedRemainderData,
    ];

    putJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
        data: $updatedRemainderData
    )
        ->assertJsonFragment($neededResponseJson)
        ->assertStatus(200)
    ;
});

test('discord user remainder update checks due_at to be in the future ', function () {
    putJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
        data: ['due_at' => fake()->dateTimeBetween('-1 day', 'now')->getTimestamp() ]
    )
        ->assertJsonValidationErrorFor('due_at')
        ->assertStatus(422)
    ;

});
