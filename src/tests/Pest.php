<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});


/* NOTE: beforeEach() cannot be applied on a group, so each file has a group definition beside the beforeEach() definitions.
// assign group 'auth-routes' to the Auth routes (see: routes/auth.php)
    uses()->group('auth-routes')->in('Feature/Auth');
    uses()->group('auth-routes')->in('Feature/ProfileTest.php');
*/


/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/



//---------------------------------------------------------------------------------------------------------------
function getJsonAuthorized(string $uri = null, array $headers = [])
{
    return test()->getJson(
        uri: $uri ?? test()->endpoint,
        headers: array_merge(
            ['Authorization' => 'Bearer '.test()->token],
            $headers,
        )
    );
}

//---------------------------------------------------------------------------------------------------------------
function postJsonAuthorized(string $uri = null, array $data = [], array $headers = [])
{
    return test()->postJson(
        uri: $uri ?? test()->endpoint,
        data: $data,
        headers: array_merge(
            ['Authorization' => 'Bearer '.test()->token],
            $headers,
        )
    );
}

//---------------------------------------------------------------------------------------------------------------
function putJsonAuthorized(string $uri = null, array $data = [], array $headers = [])
{
    return test()->putJson(
        uri: $uri ?? test()->endpoint,
        data: $data,
        headers: array_merge(
            ['Authorization' => 'Bearer '.test()->token],
            $headers,
        )
    );
}

//---------------------------------------------------------------------------------------------------------------
function deleteJsonAuthorized(string $uri = null, array $data = [], array $headers = [])
{
    return test()->deleteJson(
        uri: $uri ?? test()->endpoint,
        data: $data,
        headers: array_merge(
            ['Authorization' => 'Bearer '.test()->token],
            $headers,
        )
    );
}

//---------------------------------------------------------------------------------------------------------------
function endpoint($uri = null, array $parameters = [])
{
    return str_replace(
        array_map(fn ($item) => '{'.$item.'}', array_keys($parameters)),
        array_values($parameters),
        $uri ?? test()->endpoint
    );
}

//---------------------------------------------------------------------------------------------------------------
function skipIfNoDefaultAuth()
{
    if (!Route::has('login')) {
        test()->markTestSkipped('Default Auth Routes are disabled.');
    }
}
