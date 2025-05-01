<?php

use App\Http\Middleware\HardenHeaders;

function getHardenHeadersClassProperty(string $name): array
{

    return
        (new \ReflectionClass(HardenHeaders::class))
        ->getProperty($name)
        ->getValue(new HardenHeaders());

}

/**
 * @runInSeparateProcess
 */
test('added headers correctly ', function () {

    $headersToAdd = getHardenHeadersClassProperty('headersToAdd');

    $response = $this->get('/');

    foreach ($headersToAdd as $header => $value) {
        $this->assertTrue($response->headers->has($header));
    }

});


test('removed headers correctly ', function () {

    $headersToRemove = getHardenHeadersClassProperty('headersToRemove');

    $response = $this->get('/');

    foreach ($headersToRemove as $header => $value) {
        $this->assertFalse($response->headers->has($header));
    }

});

test('return status is 200')
    ->get('/')
    ->assertStatus(200);
