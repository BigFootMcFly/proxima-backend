<?php

return [

    'github' => [
        'repository' => env('GITHUB_REPOSITORY'),
        'token' => env('GITHUB_TOKEN'),
        'cache_ttl' => env('GITHUB_CACHE_TTL'),
    ],
    'maintenance' => [
        'keep_orphaned_remainders_for' => env('KEEP_ORPHANED_REMAINDERS_FOR', 7),
        'keep_deleted_discord_users_for' => env('KEEP_DELETED_DISCORD_USERS_FOR', 30),
        'keep_finished_remainders_for' => env('KEEP_FINISHED_REMAINDERS_FOR', 7),
    ],
    'setup' => [
        'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'admin_password' => env('ADMIN_PASSWORD', 'PlEaSe_ChAnGe_Me'),
    ],

];
