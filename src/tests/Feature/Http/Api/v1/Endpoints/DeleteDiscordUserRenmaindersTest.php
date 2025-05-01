<?php

/*
 * Endpoint: PUT /discord-users/{discord_user}/remainders/{remainder}
 */

use App\Models\DiscordUser;
use App\Models\Remainder;
use App\Models\User;

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
test('discord user remainder delete endpoint requires authorization', function () {

    $this->deleteJson(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
    )
        ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder delete endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    deleteJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
        headers: ['Authorization' => 'Bearer '.$badToken]
    )->assertStatus(403);
});

//---------------------------------------------------------------------------------------------------------------------
test('discord user remainder delete endpoint validates parameters', function () {

    // check for non-existing discord_user
    deleteJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id + 1,
            'remainder' => $this->remainder->id,
        ]),
    )
    ->assertStatus(404);

    // check for non-existing remainder
    deleteJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id + 1,
        ]),
    )
    ->assertStatus(404);

    // check for remainder not belonging to discord_user
    $secondUser = DiscordUser::factory()->create();
    deleteJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $secondUser->id,
            'remainder' => $this->remainder->id,
        ]),
    )
    ->assertJsonValidationErrorFor('remainder')
    ->assertStatus(422);

    // check for denying without snowflake
    deleteJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
    )
    ->assertJsonValidationErrorFor('snowflake')
    ->assertStatus(422);

});

test('discord user remainder delete works correctly ', function () {

    deleteJsonAuthorized(
        uri: endpoint(parameters: [
            'discord_user' => $this->discord_user->id,
            'remainder' => $this->remainder->id,
        ]),
        data: [
            'snowflake' => $this->discord_user->snowflake,
        ]
    )
    ->assertStatus(204);

    $this->assertEquals(Remainder::find($this->remainder->id), null);

});
