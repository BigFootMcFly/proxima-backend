<?php

namespace App\Filament\Resources\DiscordUserResource\Services;

use App\Models\DiscordUser;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

final class DiscordUserResourceInfoList
{

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Trashed')
                    ->schema([
                        TextEntry::make('trashed')
                        ->columnSpan(2)
                        ->view('filament.admin.trashed')
                        ->extraAttributes(['margin-bottom' => '1em;']),
                    ])
                    ->visible(fn (DiscordUser $discordUser) => $discordUser->trashed()),
                Section::make('Discrod User')
                    ->icon('heroicon-o-users')
                    ->iconColor('info')
                    ->description('The common data of the discord user.')
                    ->columns(2)
                    ->compact()
                    ->schema([
                        TextEntry::make('user_name')->label('User name'),
                        TextEntry::make('global_name')->label('Global name'),
                        TextEntry::make('snowflake')->label('Snowflake'),
                        TextEntry::make('remainders.count')
                            ->label('Remainders')
                            ->state(fn (DiscordUser $discordUser): int => $discordUser->remainders()->count()),
                        TextEntry::make('locale')->label('Locale'),
                        TextEntry::make('timezone')->label('Timezone'),
                    ]),
            ]);
    }

}
