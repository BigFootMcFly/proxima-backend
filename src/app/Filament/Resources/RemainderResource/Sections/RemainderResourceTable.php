<?php

namespace App\Filament\Resources\RemainderResource\Sections;

use App\Enums\RemainderStatus;
use App\Filament\Actions\InfoAction;
use App\Filament\Resources\DiscordUserResource\RelationManagers\RemaindersRelationManager;
use App\Filament\Resources\RemainderResource\Pages\ListRemainders;
use App\Models\Remainder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums;
use Filament\Tables\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

final class RemainderResourceTable
{

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Checks if the table is filtered by the given field
     *
     * @param string $field The name of the field to check for
     *
     * @return bool True if the table is filtered by the given field, false otherwise
     *
     */
    protected static function isTableFilteredBy(string $field): bool
    {
        $liveWire = Livewire::current();

        // hide details on relation manager
        if ($liveWire::class === RemaindersRelationManager::class) {
            return true;
        }

        // hide details if the table is filtered to one user only
        if ($liveWire->tableFilters[$field]['value'] ?? null !== null) {
            return true;
        }

        // don't hide
        return false;
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Checks if the current table is included in another view as a relation
     *
     * @return bool
     *
     * @callback_function
     *
     */
    protected static function isRelationManagerView(): bool
    {
        return is_subclass_of(
            Livewire::current(),
            RelationManager::class
        );
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Checks if the DiscordUser of the Remainder is trashed
     *
     * @param Remainder $remainder The Remainder to check
     *
     * @return bool True if the DiscordUser (owner) is trashed, false otherwise
     *
     * @callback_function
     *
     */
    protected static function isDiscordUserTrashed(Remainder $remainder): bool
    {
        //NOTE: this needs to be cached becouse this gets called for each row twice, so caching is only needed just for a short time
        return cache()->remember(
            key: 'DiscordUserIsTrashed-'.$remainder->discord_user_id,
            ttl: 3,
            callback: fn () => $remainder->discordUser->trashed()
        );
        //NOTE: vendor/livewire/livewire/src/Features/SupportModels/ModelSynth.php:65 calls this query twice,
        //         which cannot be cached sadly...
        //      SQL: select * from "discord_users" where "discord_users"."id" = 1 limit 1
        //      but 3 queries are better then 22, so...
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Checks if the table is filtered by the DiscordUser
     *
     * @return bool true if a filter is present, falser otherwise
     *
     * @callback_function
     *
     * @callback_function
     *
     */
    public static function isTableFilteredByDiscordUser(): bool
    {
        return static::isTableFilteredBy('discord_user_id');
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     *  Returns the color of a colummn based of the state of the Remainder property
     *
     * @param Remainder $record The Remainder to check if it is trashed or not (Injected by filament)
     *
     * @return string The color based on the trashed state
     *
     * @callback_function
     *
     */
    public static function getColumnColor(Remainder $record): string
    {
        return match ($record->trashed()) {
            true => 'danger-500',
            default => 'gray',
        };
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the classes of a colummn based of the Remainders state
     *
     * @param Remainder $record The Remainder to check if it is trashed or not (Injected by filament)
     *
     * @return string The class(es) based on the state of the Remainder
     *
     * @callback_function
     *
     */
    public static function getRecordClass(Remainder $record): string
    {
        if ($record->trashed()) {
            return 'border-l-4 !border-l-danger-500 line-through !decoration-danger-700 !text-amber-700';
        }

        if ($record->isOverDue()) {
            return 'border-l-4 !border-l-warning-500 !decoration-warning-700 !text-amber-700';
        }

        return 'border-l-4 !border-l-success-500 !text-yellow-400';
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the status icon based of the Remainder
     *
     * @param Remainder $record The Remainder to check if it is trashed or not (Injected by filament)
     *
     * @return string The icon of matching the Remainders status
     *
     * @callback_function
     *
     */
    public static function getStatusIcon(Remainder $record): string
    {
        return match ($record->status) {
            RemainderStatus::NEW => 'heroicon-o-clock',
            RemainderStatus::FAILED => 'heroicon-o-x-circle',
            RemainderStatus::FINISHED => 'heroicon-o-check-circle',
            default => 'heroicon-o-exclamation-circle',
        };
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the status icon color based of the Remainder
     *
     * @param Remainder $record The Remainder to check if it is trashed or not (Injected by filament)
     *
     * @return string The color of the icon of the Remainders status
     *
     * @callback_function
     *
     */
    public static function getStatusIconColor(Remainder $record): string
    {
        return match ($record->status) {
            RemainderStatus::NEW => 'info',
            RemainderStatus::PENDING => 'warning',
            RemainderStatus::FAILED => 'danger',
            RemainderStatus::FINISHED => 'success',
            default => 'gray',
        };
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the status icon color based of the Remainder
     *
     * @param Remainder $record The Remainder to check if it is trashed or not (Injected by filament)
     *
     * @return string The color of the icon of the Remainders status
     *
     * @callback_function
     *
     */
    public static function getDueAtIconColor(Remainder $record): string
    {
        return $record->isOverDue() ? 'danger' : 'gray';
    }


    //---------------------------------------------------------------------------------------------------------------
    public static function table(Table $table): Table
    {

        return $table
            ->recordClasses(self::getRecordClass(...))
            ->columns([

                ColumnGroup::make('Discord user')
                    ->columns([

                        TextColumn::make('id')
                            ->color(self::getColumnColor(...))
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->numeric()
                            ->sortable(),

                        TextColumn::make('discordUser.user_name')
                            ->color(self::getColumnColor(...))
                            ->hidden(self::isTableFilteredByDiscordUser(...))
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->sortable()
                            ->searchable(isIndividual: true)
                            ->label('Name'),

                        TextColumn::make('discordUser.global_name')
                            ->color(self::getColumnColor(...))
                            ->hidden(self::isTableFilteredByDiscordUser(...))
                            ->sortable()
                            ->searchable(isIndividual: true)
                            ->label('Global Name'),

                    ])->alignCenter(),

                ColumnGroup::make('Remainder')
                    ->columns([

                        IconColumn::make('status')
                            ->sortable()
                            ->alignCenter()
                            ->icon(self::getStatusIcon(...))
                            ->color(self::getStatusIconColor(...))
                            ->extraAttributes(fn ($state) => ['title' => $state->value]),

                        TextColumn::make('due_at')
                            ->color(self::getColumnColor(...))
                            ->dateTime()
                            ->timezone(Auth::user()->timezone)
                            ->sortable()
                            ->icon('heroicon-m-clock')
                            ->iconColor(self::getDueAtIconColor(...)),

                        TextColumn::make('message')
                            ->color(self::getColumnColor(...))
                            ->searchable()
                            ->limit(50),

                        TextColumn::make('channel_id')
                            ->searchable()
                            ->color(self::getColumnColor(...))
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->placeholder('No chanel set.'),

                    ])->alignCenter(),

                ColumnGroup::make('Dates')
                    ->columns([

                        TextColumn::make('created_at')
                            ->color(self::getColumnColor(...))
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->dateTime()
                            ->sortable(),

                        TextColumn::make('updated_at')
                            ->color(self::getColumnColor(...))
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->dateTime()
                            ->sortable(),

                        TextColumn::make('deleted_at')
                            ->color(self::getColumnColor(...))
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->dateTime()
                            ->sortable(),

                    ])->alignCenter(),
            ])
            ->defaultSort('due_at')
            ->filters([

                SelectFilter::make('discord_user_id')
                    ->relationship(
                        name: 'discordUser',
                        titleAttribute: 'user_name',
                        modifyQueryUsing: fn (Builder $query, $livewire) =>
                            ($discordUserId = $livewire->tableFilters['discord_user_id']['value']) !== null
                                ? $query->where('id', $discordUserId)
                                : $query
                    )
                    ->searchable()
                    ->preload() //NOTE: this is very resource intensive for large data sets
                    ->label('Discord User')
                    ->hidden(static::isRelationManagerView(...))
                    ->resetState(fn () => redirect(ListRemainders::getUrl())),

                SelectFilter::make('status')
                    ->multiple()
                    ->hidden(static::isRelationManagerView(...))
                    ->options(RemainderStatus::toSelectOptions()),

                TernaryFilter::make('channel_id')
                    ->hidden(self::isTableFilteredByDiscordUser(...))
                    ->label('Channel')
                    ->placeholder('All')
                    ->trueLabel('Provided')
                    ->falseLabel('Not provided')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('channel_id'),
                        false: fn (Builder $query) => $query->whereNull('channel_id'),
                        blank: fn (Builder $query) => $query,
                    ),

                TrashedFilter::make()
                    ->hidden(self::isTableFilteredByDiscordUser(...)),

            ], layout: Enums\FiltersLayout::Dropdown)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->filtersFormColumns(4)

            ->actions([
                InfoAction::make(),

                EditAction::make()
                    ->label('')
                    ->extraAttributes(['title' => 'Edit'])
                    ->closeModalByClickingAway(false),

                DeleteAction::make()->label('')->extraAttributes(['title' => 'Delete']),

                RestoreAction::make()
                    ->disabled(self::isDiscordUserTrashed(...))
                    ->label('')
                    ->iconButton()
                    ->color('danger')
                    ->iconSize(IconSize::Small)
                    ->extraAttributes(
                        fn (Remainder $remainder) =>
                        self::isDiscordUserTrashed($remainder)
                            ? ['title' => 'Cannot Restore']
                            : ['title' => 'Restore']
                    ),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function (Actions\DeleteBulkAction $action) {
                            $action->redirect('/admin/remainders');
                        }),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);

    }

}
