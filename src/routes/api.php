<?php

use App\Enums\ApiPermission;
use App\Http\Controllers\Api\v1\DiscordUserBySnoflakeController;
use App\Http\Controllers\Api\v1\DiscordUserController;
use App\Http\Controllers\Api\v1\DiscordUserRemaindersController;
use App\Http\Controllers\Api\v1\RemainderByDueAtController;
use App\Http\Middleware\StripPaginationInfo;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum',ability(ApiPermission::ManageDiscordUsers)])->group(function () {
    Route::apiResource('discord-users', DiscordUserController::class)->middleware(StripPaginationInfo::class);
});

Route::middleware(['auth:sanctum',ability(ApiPermission::ManageDiscordUserRemainders)])->group(function () {
    Route::get('discord-users/{discord_user}/remainders', [DiscordUserRemaindersController::class, 'index'])
        ->name('discord-user.remainders.index')
        ->middleware(StripPaginationInfo::class);

    Route::post('discord-users/{discord_user}/remainders', [DiscordUserRemaindersController::class, 'store'])
        ->name('discord-user.remainders.store');

    Route::put('discord-users/{discord_user}/remainders/{remainder}', [DiscordUserRemaindersController::class, 'update'])
        ->name('discord-user.remainders.update');

    Route::delete('discord-users/{discord_user}/remainders/{remainder}', [DiscordUserRemaindersController::class, 'destroy'])
        ->name('discord-user.remainders.destroy');
});

Route::middleware(['auth:sanctum',ability(ApiPermission::ManageDiscordUserBySnowflake)])->group(function () {
    Route::get('discord-user-by-snowflake/{discord_user:snowflake}', [DiscordUserBySnoflakeController::class, 'show'])
        ->name('discord_user_by_snowflake.index');
    Route::put('discord-user-by-snowflake/{snowflake}', [DiscordUserBySnoflakeController::class, 'update'])
        ->name('discord_user_by_snowflake.update');
});

Route::middleware(['auth:sanctum',ability(ApiPermission::GetRemaindersByDueAt)])->group(function () {
    Route::get('remainder-by-due-at/{due_at}', RemainderByDueAtController::class)
        ->name('remainder_by_due_at.index')
        ->middleware(StripPaginationInfo::class);
});
