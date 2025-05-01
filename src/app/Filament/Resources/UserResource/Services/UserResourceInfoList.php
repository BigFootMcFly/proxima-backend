<?php

namespace App\Filament\Resources\UserResource\Services;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

final class UserResourceInfoList
{

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make('User')
                ->icon('heroicon-o-users')
                ->iconColor('info')
                ->description('The common data of the user.')
                ->columns(2)
                ->compact()
                ->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('email')->label('Email'),
                    TextEntry::make('timezone')->label('Timezone'),
                    TextEntry::make('email_verified_at')->label('Verified')->placeholder('not verified'),
                ]),
        ]);
    }
}
