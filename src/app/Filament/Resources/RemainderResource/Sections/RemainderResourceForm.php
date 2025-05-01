<?php

namespace App\Filament\Resources\RemainderResource\Sections;

use App\Enums\RemainderStatus;
use App\Filament\Resources\RemainderResource\Pages\ListRemainders;
use App\Http\Requests\Api\v1\StoreRemainderRequest;
use App\Models\Remainder;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

final class RemainderResourceForm
{

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the DiscordUser ID from the current table filter
     *
     * @param HasTable $livewire The current livewire componennt (automatically injected by filament)
     *
     * @return int|null The ID of the DiscordUser if the filtering is used, null otherwise
     *
     * @callback_function
     *
     */
    public static function getFilteredDiscordUserId(HasTable $livewire): ?int
    {
        return $livewire->getTableFilterState('discord_user_id')['value'];
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Checks if the table is filtered by DiscordUser
     *
     * @param HasTable $livewire The current livewire componennt (automatically injected by filament)
     *
     * @return bool True if a filter is present, false otherwise
     *
     * @callback_function
     *
     */
    public static function isNotFilteredByDiscordUser(HasTable $livewire): bool
    {
        return null === self::getFilteredDiscordUserId($livewire) ;
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        $rules = StoreRemainderRequest::asFilamentRules();

        return $form
            ->schema([
                Select::make('discord_user_id')
                    ->prefixIcon('heroicon-o-users')
                    ->relationship(
                        name: 'discordUser',
                        titleAttribute: 'user_name',
                        //NOTE: hidden()/disabled() removes the field from the data sent to the server, so we filter it instead
                        modifyQueryUsing: fn (Builder $query, ListRemainders $livewire) =>
                          ($discordUserId = self::getFilteredDiscordUserId($livewire)) !== null
                                ? $query->where('id', $discordUserId)
                                : $query
                    )
                    ->searchable(self::isNotFilteredByDiscordUser(...))
                    ->preload(self::isNotFilteredByDiscordUser(...))
                    ->default(self::getFilteredDiscordUserId(...))
                    ->native(false)
                    ->visibleOn('create')
                    ->selectablePlaceholder(false)
                    ->required()
                    ->columnSpan(2),
                Placeholder::make('discordUser.user_name')
                    ->content(fn (Remainder $remainder): string => $remainder->discordUser->user_name)
                    ->hiddenOn(['create']),
                Placeholder::make('discordUser.global_name')
                    ->content(fn (Remainder $remainder): string => $remainder->discordUser->global_name)
                    ->hiddenOn(['create']),
                DateTimePicker::make('due_at')
                    ->prefixIcon('heroicon-o-calendar')
                    ->required()
                    ->native(false)
                    ->timezone(Auth::user()->timezone)
                    ->rules($rules['due_at'])
                    ->default(Carbon::now()->addMinutes(10)),
                TextInput::make('channel_id')
                    ->prefixIcon('heroicon-o-hashtag')
                    ->placeholder('snowflake or leave empty')
                    ->rules($rules['channel_id']),
                TextInput::make('message')
                    ->required()
                    ->prefixIcon('heroicon-o-chat-bubble-bottom-center-text')
                    ->placeholder('the message tor the remainder')
                    ->rules($rules['message'])
                    ->columnSpan(2),
                Select::make('status')
                    ->visibleOn('edit')
                    ->options(RemainderStatus::toSelectOptions())
                    ->selectablePlaceholder(false)
                    ->columnSpan(2)
                    ->default(RemainderStatus::NEW),
            ]);
    }

}
