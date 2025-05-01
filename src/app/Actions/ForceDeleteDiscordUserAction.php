<?php

namespace App\Actions;

use App\Models\DiscordUser;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Gate;

/**
 * Deletes the DiscordUser
 *
 * NOTE: performs permanent delete
 */
class ForceDeleteDiscordUserAction
{

    /**
     * Deletes the DiscordUser with all it's remainders
     *
     * @param DiscordUser $discordUser The DiscordUser to delete
     *
     * @return Response|ResponseFactory [204] Returns an empty response
     *
     */
    public static function run(DiscordUser $discordUser): Response|ResponseFactory
    {
        Gate::authorize('forcedelete', $discordUser);

        $discordUser->allRemainders()->forceDelete();

        $discordUser->permanentDelete();

        return response(status: 204);
    }

}
