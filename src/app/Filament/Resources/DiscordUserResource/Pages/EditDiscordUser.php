<?php

namespace App\Filament\Resources\DiscordUserResource\Pages;

use App\Actions\DeleteDiscordUserAction;
use App\Actions\ForceDeleteDiscordUserAction;
use App\Actions\RestoreDiscordUserAction;
use App\Filament\Resources\DiscordUserResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\DiscordUser;
use Filament\Actions\Action;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EditDiscordUser extends EditRecord
{
    protected static string $resource = DiscordUserResource::class;

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Restores trashed Discord User Action
     */
    public static function restoreAction(Page $page): Action
    {
        $trashedRemainderCount = $page->record->trashedRemainderCount;
        return RestoreAction::make()
            ->requiresConfirmation()
            ->modalDescription('Are you sure you\'d like to restore this discord user and all it\'s remainders?')
            ->modalIcon('heroicon-o-arrow-path')
            ->badge($trashedRemainderCount)
            ->badgeColor(
                fn () => match ($trashedRemainderCount) {
                    0 => 'gray',
                    default => 'warning'
                }
            )
            ->action(function (DiscordUser $discordUser) use ($page) {
                RestoreDiscordUserAction::run($discordUser);
                Notification::make()
                    ->title("Discord user \"{$discordUser->user_name}\" restored.")
                    ->success()
                    ->send();
                cache()->forget("DiscordUserRemainderCount_{$discordUser->id}");
                $page->redirect(EditDiscordUser::getUrl(['record' => $discordUser]));
            });
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Delete Discord User Action
     *
     * NOTE: only performs softdelete
     */
    public static function deleteAction(Page $page): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalDescription('Are you sure you\'d like to delete this discord user and all it\'s remainders?')
            ->modalIcon('heroicon-o-trash')
            ->label('Delete')
            ->color('danger')
            ->badge($page->record->RemainderCount)
            ->badgeColor(
                fn () => match ($page->record->RemainderCount) {
                    0 => 'gray',
                    default => 'warning'
                }
            )
            ->action(function (DiscordUser $discordUser) use ($page) {
                DeleteDiscordUserAction::run($discordUser);
                Notification::make()
                    ->title('Discord user "'.$page->record->user_name.'" deleted.')
                    ->success()
                    ->send();
                cache()->forget('DiscordUserTrashedRemainderCount_'.$discordUser->id);
                $page->redirect(EditDiscordUser::getUrl(['record' => $discordUser]));
            })
            ->hidden(fn (DiscordUser $discordUser) => $discordUser->trashed());
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Delete Discord User Action
     *
     * NOTE: performs permanent delete
     */
    public static function forceDeleteAction(Page $page): Action
    {
        return ForceDeleteAction::make()
            ->requiresConfirmation()
            ->modalDescription('Are you sure you\'d like to PERMANENTLY delete this discord user and all it\'s remainders?')
            ->modalIcon('heroicon-o-trash')
            ->color('danger')
            ->badge($page->record->allRemainderCount)
            ->badgeColor(
                fn () => match ($page->record->remainders_count) {
                    0 => 'gray',
                    default => 'warning'
                }
            )
            ->action(function (DiscordUser $discordUser) use ($page) {
                ForceDeleteDiscordUserAction::run($discordUser);
                Notification::make()
                    ->title("Discord user \"{$discordUser->global_name}\" deleted.")
                    ->success()
                    ->send();
                $page->redirect(ListDiscordUsers::getUrl());
            });
    }

    //---------------------------------------------------------------------------------------------------------------
    protected function getHeaderActions(): array
    {
        return [
            self::deleteAction($this),
            self::forceDeleteAction($this),
            self::restoreAction($this),
        ];
    }
}
