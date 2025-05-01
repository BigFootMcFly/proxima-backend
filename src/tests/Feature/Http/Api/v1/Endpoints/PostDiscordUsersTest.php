<?php

/*
 * Endpoint: POST /discord-users
 */

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
test('create discord user fails without post data', function () {
    postJsonAuthorized()
        ->assertStatus(422);
});

//---------------------------------------------------------------------------------------------------------------------
test('create discord user succeds with snowflake only', function () {
    postJsonAuthorized(
        data: [
            'snowflake' => fake()->numerify(str_repeat('#', 18)),
        ]
    )->assertStatus(201);
});

//---------------------------------------------------------------------------------------------------------------------
test('create discord user fails with bad locale data', function () {
    postJsonAuthorized(
        data: [
            'snowflake' => fake()->numerify(str_repeat('#', 18)),
            'locale' => 'bad_LOCALE',
        ]
    )->assertStatus(422);
});

//---------------------------------------------------------------------------------------------------------------------
test('create discord user fails with bad timezone data', function () {
    postJsonAuthorized(
        data: [
            'snowflake' => fake()->numerify(str_repeat('#', 18)),
            'timezone' => 'Badlands/SunkenCity',
        ]
    )->assertStatus(422);
});

//---------------------------------------------------------------------------------------------------------------------
test('create discord user succeds with good data values', function () {
    $data = [
        'timezone' => 'Europe/Budapest',
        'locale' => 'hu_HU',
        'snowflake' => fake()->numerify(str_repeat('#', 18)),
    ];
    postJsonAuthorized(data: $data)
        ->assertStatus(201)
        ->assertJsonPath('data.snowflake', $data['snowflake'])
        ->assertJsonPath('data.timezone', $data['timezone'])
        ->assertJsonPath('data.locale', $data['locale']);
});
