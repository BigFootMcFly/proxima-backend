<?php

use App\Enums\ApiPermission;

/**
 * Returns a ability filter of az ApiPermission case for 'auth:sanctum' 'ability' middleware.
 *
 * @param ApiPermission $ability The ability to generate a filter for
 *
 * @return string The ability string. Example: 'ability:auth-user'
 *
 * @see https://laravel.com/docs/11.x/sanctum#token-ability-middleware
 *
 */
function ability(ApiPermission $ability): string
{
    return "ability:{$ability->value}";
}
