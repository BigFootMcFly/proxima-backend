<?php

/*
 * Endpoint: GET /remainder-by-due-at/{due_at}
 */

use App\Http\Resources\Api\v1\RemainderResource;
use App\Models\Remainder;
use App\Models\User;

beforeEach(function () {
    $this->endpoint = '/api/v1/remainder-by-due-at/{due_at}';
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('api-v1', ['remainders-by-due-at'])->plainTextToken;
});

//---------------------------------------------------------------------------------------------------------------------
test('remainders by due-at endpoint requires authorization', function () {
    $this->getJson(endpoint())
        ->assertStatus(401);
});

//---------------------------------------------------------------------------------------------------------------------
test('remainders by due-at endpoint denies bad token', function () {
    $badToken = $this->user->createToken('bad-token', ['bad-ability'])->plainTextToken;
    getJsonAuthorized(headers: ['Authorization' => 'Bearer '.$badToken])
        ->assertStatus(403);
});

//---------------------------------------------------------------------------------------------------------------------
test('remainders by due-at endpoint accepts good token', function () {
    getJsonAuthorized(uri: endpoint(parameters: ['due_at' => time()]))
        ->assertStatus(200);
});

//---------------------------------------------------------------------------------------------------------------------
test('remainders by due-at return empty list on empty database', function () {
    getJsonAuthorized(uri: endpoint(parameters: ['due_at' => time()]))
        ->assertJsonCount(0, 'data');
});

//---------------------------------------------------------------------------------------------------------------------
test('remainders by due-at endpoint returns the correct result', function () {
    $remainder = Remainder::factory()->create();
    $responseData = [
        json_decode((RemainderResource::make($remainder))->toJson(), true),
    ];
    getJsonAuthorized(uri: endpoint(parameters: ['due_at' => $remainder->due_at->timestamp]))
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data', $responseData)
    ;
});

//---------------------------------------------------------------------------------------------------------------------
test('remainders by due-at list pagination is sanitized', function () {
    $remainder = Remainder::factory()->create();
    getJsonAuthorized(uri: endpoint(parameters: ['due_at' => $remainder->due_at->timestamp]))
        ->assertJsonMissingPath('links')
        ->assertJsonMissingPath('meta.links')
        ->assertJsonMissingPath('meta.path')
    ;
});
