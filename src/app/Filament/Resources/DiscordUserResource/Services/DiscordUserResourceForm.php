<?php

namespace App\Filament\Resources\DiscordUserResource\Services;

use App\Http\Requests\Api\v1\StoreDiscordUserRequest;
use App\Http\Requests\Api\v1\UpdateDiscordUserRequest;
use App\Models\DiscordUser;
use Filament\Forms\Form;
use Filament\Forms;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

final class DiscordUserResourceForm
{

    public static function form(Form $form): Form
    {

        $rules = match ($form->getOperation()) {
            'edit' => UpdateDiscordUserRequest::asFilamentRules(),
            'create' => StoreDiscordUserRequest::asFilamentRules(),
            default => [],
        };

        return $form
            ->schema([
                Forms\Components\Placeholder::make('trashed')
                    ->columnSpan(2)
                    ->view('filament.admin.trashed')
                    ->visible(fn (DiscordUser $discordUser) => $discordUser->trashed()),
                Forms\Components\TextInput::make('snowflake')
                    ->rules($rules['snowflake'] ?? [])
                    ->visibleOn('create')
                    ->disabledOn('edit'),
                Forms\Components\Placeholder::make('snowflake')
                    ->content(fn (DiscordUser $discordUser): string => $discordUser->snowflake)
                    ->hiddenOn(['create']),
                Forms\Components\TextInput::make('user_name')
                    ->rules($rules['user_name'] ?? []),
                Forms\Components\TextInput::make('global_name')
                    ->rules($rules['global_name'] ?? []),
                Forms\Components\TextInput::make('avatar')
                    ->rules($rules['avatar'] ?? []),
                Forms\Components\Select::make('locale')
                    ->rules($rules['locale'] ?? [])
                    ->options(array_combine(LOCALES, LOCALES)),
                TimezoneSelect::make('timezone')
                    ->searchable(),
            ]);
    }

}
