<?php

use App\Actions\PruneOrphanedRemaindersAction;
use App\Actions\RemoveDeletedDiscordUsersAction;
use App\Actions\RemoveFinishedRemaindersAction;
use App\Http\Middleware\HardenHeaders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
        $middleware->append(HardenHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportDuplicates();

        // hide model details from API response
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Not Found.'], 404);
            };
        });

    })
    ->withSchedule(function (Schedule $schedule) {
        // send hourly notification to log
        $schedule->call(fn () => Log::info('Watchdog: scheduler running.'))->hourly();
        // run database cleanup routines
        $schedule->call(new RemoveFinishedRemaindersAction())->dailyAt('00:24');
        $schedule->call(new RemoveDeletedDiscordUsersAction())->dailyAt('00:32');
        $schedule->call(new PruneOrphanedRemaindersAction())->dailyAt('00:42');
    })
    ->create();
