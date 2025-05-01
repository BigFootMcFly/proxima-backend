<?php

namespace App\Filament\Resources\RemainderResource\Sections;

use App\Models\Remainder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

final class RemainderResourceInfoList
{

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the color of a field based of the Remainders state
     *
     * @param Remainder $record The Remainder to get the class for (Injected by filament)
     *
     * @return string The color based on the state of the Remainder
     *
     */
    public static function getStatusColor(Remainder $record): string
    {
        return match ($record->isFailed()) {
            true => 'danger',
            default => 'info',
        };
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Creates an infoList view for RemainderResource
     *
     * @param Infolist $infolist The current InfoList (Injected by filament)
     *
     * @return Infolist The created infoList
     *
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Discrod User')
                    ->icon('heroicon-o-users')
                    ->iconColor('info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('discordUser.user_name')->label('User name'),
                        TextEntry::make('discordUser.global_name')->label('Global name'),
                    ]),
                Section::make('Remainder')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('due_at')->dateTime(),
                        TextEntry::make('channel_id')->placeholder('No chanel set.'),
                        TextEntry::make('message')->columnSpan(2),
                        TextEntry::make('status')->color(self::getStatusColor(...)),
                        TextEntry::make('error')
                            ->placeholder('no error')
                            ->color(self::getStatusColor(...)),
                    ]),
            ]);
    }

}
