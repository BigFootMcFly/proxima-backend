<?php

namespace App\Filament\Resources\DiscordUserResource\Services;

use App\Filament\Actions\InfoAction;
use App\Models\DiscordUser;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

final class DiscordUserResourceTable
{

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the RemainderResource::ListRemainders view filtered by the DiscordUser
     *
     * @param DiscordUser $discordUser The DiscordUser to view
     *
     * @return string The route for the view
     *
     * @callback_function
     *
     */
    public static function getRemainderActionUrl(DiscordUser $discordUser): string
    {
        return route('filament.admin.resources.remainders.index', [
            'tableFilters' => [
                'discord_user_id' =>
                    ['value' => $discordUser->id],
            ],
        ]);
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Creates an Action to navigate to RemainderResource::ListRemainders view filtered by the DiscordUser
     *
     * @return Action The action to navigate to the view
     *
     */
    public static function createRemainderAction(): Action
    {
        return Action::make('Remainders')
            ->label('')
            ->extraAttributes(['title' => 'Remainders'])
            ->icon('heroicon-o-calendar')
            ->url(self::getRemainderActionUrl(...));
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the RemainderResource::ViewRemainder view
     *
     * @param DiscordUser $discordUser  The DiscordUser to view
     *
     * @return string The route for the view
     *
     * @callback_function
     *
     */
    public static function getViewActionUrl(DiscordUser $discordUser): string
    {
        return route('filament.admin.resources.discord-users.show', [
            'record' => $discordUser,
        ]);
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Creates an Action to navigate to RemainderResource::ViewRemainder view
     *
     * @return Action The action to navigate to the view
     *
     */
    public static function createViewAction(): Action
    {
        return Action::make('View')
            ->label('')
            ->extraAttributes(['title' => 'View'])
            ->icon('heroicon-o-eye')
            ->url(self::getViewActionUrl(...));
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the classes of a colummn based of the Remainders state
     *
     * @param DiscordUser $record The Remainder to get the class for (Injected by filament)
     *
     * @return string The class(es) based on the state of the Remainder
     *
     * @callback_function
     *
     */
    public static function getRecordClass(DiscordUser $record): string
    {
        if ($record->trashed()) {
            return 'border-l-4 !border-l-danger-500 line-through !decoration-danger-700 !text-amber-700';
        }

        return 'border-l-4 !border-l-success-500 !text-yellow-400';
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Deletes multiple Discord Users Action
     *
     * NOTE: performs permanent delete
     */
    public static function forceDeleteBulkAction(): ForceDeleteBulkAction
    {
        $action = ForceDeleteBulkAction::make()
            ->requiresConfirmation()
            ->modalDescription('Are you sure you\'d like to PERMANENTLY delete this discord user(s) and all it\'s remainders?')
            ->modalIcon('heroicon-o-trash')
            ->color('danger');

        $action->action(function () use ($action): void {
            $action->process(static function (Collection $records): void {
                $records->each(fn (DiscordUser $record) => $record->permanentDelete());
            });
            $action->success();
        });

        return $action;
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(self::getRecordClass(...))
            ->columns([
                TextColumn::make('snowflake')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_name')
                    ->searchable(),
                TextColumn::make('global_name')
                    ->searchable(),
                TextColumn::make('remainders_count')
                    ->label('Remainders')
                    ->badge()
                    ->counts('remainders')
                    ->sortable(),
                TextColumn::make('locale')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('timezone')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                InfoAction::make(),
                self::createViewAction(),
                self::createRemainderAction(),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->extraAttributes(['title' => 'Edit']),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    self::forceDeleteBulkAction(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

}
