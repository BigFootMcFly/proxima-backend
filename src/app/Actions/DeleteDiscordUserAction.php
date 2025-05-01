<?php

namespace App\Actions;

use App\Models\DiscordUser;
use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Gate;

/**
 * Deletes the DiscordUser
 *
 * NOTE: only performs softdelete
 */
class DeleteDiscordUserAction
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
        Gate::authorize('delete', $discordUser);

        $discordUser->remainders()->delete();

        $discordUser->delete();

        return response(status: 204);
    }

}
