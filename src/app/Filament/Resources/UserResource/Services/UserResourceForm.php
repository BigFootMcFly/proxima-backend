<?php

namespace App\Filament\Resources\UserResource\Services;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

final class UserResourceForm
{

    //---------------------------------------------------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                TimezoneSelect::make('timezone')
                    ->searchable(),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->default(now())
                    ->native(false)
                    ->suffixAction(
                        Action::make('now')
                            ->icon('heroicon-o-clock')
                            ->name('now')
                            ->action(function (DateTimePicker $component, Set $set) {
                                $set($component->getName(), now()->format($component->getFormat()));
                            })
                            ->visible(
                                fn (User $user, $livewire): bool =>
                                    $livewire instanceof EditRecord
                                    && !$user->isEmailVerified()
                            )
                    ),
            ])
        ;
    }

}
