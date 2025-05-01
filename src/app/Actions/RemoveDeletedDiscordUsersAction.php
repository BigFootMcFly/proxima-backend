<?php

namespace App\Actions;

use App\Models\DiscordUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Removes finished Remainders
 *
 * Removes finished/cancelled/deleted Remainders after the keep period.
 *
 * NOTE: performs permanent delete
 */
class RemoveDeletedDiscordUsersAction
{
    /**
     * Invoke the class instance.
     *
     * @param bool $dryRun If true, only logging is performed, nothing will be deleted, otherwise matching DiscordUsers will be deleted
     *
     * @return void
     *
     */
    public function __invoke(bool $dryRun = false): void
    {
        // how long should deleted DiscordUsers kept
        $daysToKeep = config('proxima.maintenance.keep_deleted_discord_users_for');

        // get deleted DiscordUsers
        $deleted = DiscordUser::onlyTrashed()->where('deleted_at', '<', Carbon::now()->subDays($daysToKeep));
        $deletedCount = $deleted->count();

        // create message to log
        $logMessage = "{$deletedCount} DiscordUsers(s) are deleted, ";
        $logMessage .= $dryRun ? "(DRY RUN) Keeping deleted DiscordUsers." : "Removing {$deletedCount} deleted DiscordUsers.";

        // log the message
        Log::info($logMessage);

        // remove deleted DiscordUsers
        if (false === $dryRun) {
            $deleted->each(fn (DiscordUser $discordUser) => $discordUser->permanentDelete());
        }

    }
}
