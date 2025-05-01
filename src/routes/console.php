<?php

use App\Actions\PruneOrphanedRemaindersAction;
use App\Actions\RemoveDeletedDiscordUsersAction;
use App\Actions\RemoveFinishedRemaindersAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

//-------------------------------------------------------------------------------------------------------------------
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//-------------------------------------------------------------------------------------------------------------------
Artisan::command('state:initialized {--q|quiet}', function () {

    $silent = $this->option('quiet');
    $appKey = config('app.key');
    $initalized = null !== $appKey && '' !== $appKey;

    if ($initalized) {
        if (!$silent) {
            $this->info('true');
        }
        return 0;
    }

    if (!$silent) {
        $this->info('false');
    }
    return 1;

})->purpose('Checks, if the application is initalized');

//-------------------------------------------------------------------------------------------------------------------
Artisan::command("cli:info {message}", function (string $message) {
    $this->components->info($message);
})->purpose('Display colored information message to the stdout');

//-------------------------------------------------------------------------------------------------------------------
Artisan::command("cli:error {message}", function (string $message) {
    $this->components->error($message);
})->purpose('Display colored error message to the stdout');

//-------------------------------------------------------------------------------------------------------------------
Artisan::command('maintain:prune-remainder {--dry-run}', function () {
    $dryRun = $this->option('dry-run');
    (new PruneOrphanedRemaindersAction())($dryRun);
})->purpose('Removes unhandled/crashed Remainders with overdue schedules');

//-------------------------------------------------------------------------------------------------------------------
Artisan::command('maintain:clean-remainder {--dry-run}', function () {
    $dryRun = $this->option('dry-run');
    (new RemoveFinishedRemaindersAction())($dryRun);
})->purpose('Removes finished/cancelled/deleted Remainders after the keep period.');

//-------------------------------------------------------------------------------------------------------------------
Artisan::command('maintain:clean-discorduser {--dry-run}', function () {
    $dryRun = $this->option('dry-run');
    (new RemoveDeletedDiscordUsersAction())($dryRun);
})->purpose('Removes deleted DiscordUsers after the keep period.');

//-------------------------------------------------------------------------------------------------------------------
Artisan::command('maintain:clean-database {--dry-run}', function () {
    $dryRun = $this->option('dry-run');
    (new RemoveDeletedDiscordUsersAction())($dryRun);
    (new RemoveFinishedRemaindersAction())($dryRun);
    (new PruneOrphanedRemaindersAction())($dryRun);
})->purpose('Cleans up softdeleted records from the database after their specified keep time period.');
