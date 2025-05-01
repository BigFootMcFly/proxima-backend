<?php

namespace App\Actions;

use App\Enums\RemainderStatus;
use App\Models\Remainder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Removes finished Remainders
 *
 * Removes finished/cancelled/deleted Remainders after the keep period.
 *
 * NOTE: performs permanent delete
 */
class RemoveFinishedRemaindersAction
{

    /**
     * Invoke the class instance.
     *
     * @param bool $dryRun If true, only logging is performed, nothing will be deleted, otherwise matching Remainders will be deleted
     *
     * @return void
     *
     */
    public function __invoke(bool $dryRun = false): void
    {
        // how long should finished remainders kept
        $daysToKeep = config('proxima.maintenance.keep_finished_remainders_for');

        // get finished remainders
        $finished = Remainder::withTrashed()->where('due_at', '<', Carbon::now()->subDays($daysToKeep))
            ->whereIn('status', [
                RemainderStatus::FAILED,
                RemainderStatus::FINISHED,
                RemainderStatus::CANCELLED,
                RemainderStatus::DELETED,
            ]);
        $finishedCount = $finished->count();

        // create message to log
        $logMessage = "{$finishedCount} Remainder(s) are finished, ";
        $logMessage .= $dryRun ? "(DRY RUN) Keeping finished remainders." : "Removing {$finishedCount} finished remainders.";

        // log the message
        Log::info($logMessage);

        // remove finished remainders
        if (false === $dryRun) {
            $finished->forceDelete();
        }

    }
}
