<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\ViewAction;
use Filament\Support\Enums\IconSize;

/**
 * Modal ViewAction
 */
class InfoAction
{

    /**
     * Creates a modal 'info' view action
     *
     * @return ViewAction
     *
     */
    public static function make(): ViewAction
    {
        return ViewAction::make('info')
            ->label('')
            ->icon('heroicon-o-information-circle')
            ->iconSize(IconSize::Small)
            ->iconButton()
            ->color('warning')
            ->extraAttributes(
                [
                    'title' => 'Info',
                ]
            );
    }
}
