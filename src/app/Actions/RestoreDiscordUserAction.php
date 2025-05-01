<?php

namespace App\Actions;

use App\Http\Resources\Api\v1\DiscordUserResource;
use App\Models\DiscordUser;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * Restores a trashed DiscordUser
 */
class RestoreDiscordUserAction
{

    /**
     * Restores the trashed DiscordUser
     *
     * @param DiscordUser $discordUser The DiscordUser to restore
     *
     * @return ResponseFactory|Response [200] The DiscordUser
     *
     */
    public static function run(DiscordUser $discordUser): ResponseFactory|Response
    {
        Gate::authorize('restore', $discordUser);

        $trashedRemainders = $discordUser->remainders()->onlyTrashed();

        $trashedRemainders->restore();

        $discordUser->restore();

        return response(
            status: 200,
            content: [
                'data' => DiscordUserResource::make($discordUser),
            ],
        );
    }

}
