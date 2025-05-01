<?php

namespace App\Filament\Widgets;

use App\Models\DiscordUser;
use App\Models\Remainder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $discordUserCount = DiscordUser::count();
        $remainderCount = Remainder::count();
        $avarageRemainders = $discordUserCount === 0 ? 0 : $remainderCount / $discordUserCount;
        return [
            Stat::make('Discord users', Number::format($discordUserCount)),
            Stat::make('Remainders', Number::format($remainderCount)),//abbreviate
            Stat::make('Average remainders per user', Number::format($avarageRemainders, 2)),
        ];
    }
}
