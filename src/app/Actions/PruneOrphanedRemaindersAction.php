<?php

namespace App\Actions;

use App\Enums\RemainderStatus;
use App\Models\Remainder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Removes orphaned Remainders
 *
 * Removes unhandled/crashed Remainders with overdue schedules.
 *
 * NOTE: performs permanent delete
 */
class PruneOrphanedRemaindersAction
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
        // how long should orphaned Remainders kept
        $daysToKeep = config('proxima.maintenance.keep_orphaned_remainders_for');

        // get orphaned remainders
        $orphaned = Remainder::where('due_at', '<', Carbon::now()->subDays($daysToKeep))
            ->whereIn('status', [
                RemainderStatus::NEW,
                RemainderStatus::PENDING,
            ]);
        $orphanedCount = $orphaned->count();

        // create message to log
        $logMessage = "{$orphanedCount} Remainder(s) are orphaned, ";
        $logMessage .= $dryRun ? "(DRY RUN) Keeping orphaned remainders." : "Pruning {$orphanedCount} orphaned remainders.";

        // choose log level and log the message
        $logLevel = $orphanedCount > 0 ? 'warning' : 'info';
        Log::$logLevel($logMessage);

        // remove orphaned remainders
        if (false === $dryRun) {
            $orphaned->forceDelete();
        }

    }
}
