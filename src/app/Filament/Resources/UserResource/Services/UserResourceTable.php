<?php

namespace App\Filament\Resources\UserResource\Services;

use App\Filament\Actions\InfoAction;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;

final class UserResourceTable
{

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Returns the url for the UserResource::ViewUser
     *
     * @param User $user The User to view
     *
     * @return string The url to the view
     *
     * @callback_function
     *
     */
    public static function getViewUserUrl(User $user): string
    {
        return route('filament.admin.resources.users.show', [
            'record' => $user,
        ]);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('not verified')
                    ->sortable(),
                Tables\Columns\TextColumn::make('timezone')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                InfoAction::make(),
                Tables\Actions\Action::make('View')
                    ->label('')
                    ->extraAttributes(['title' => 'View'])
                    ->icon('heroicon-o-eye')
                    ->url(self::getViewUserUrl(...)),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->extraAttributes(['title' => 'Edit']),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
