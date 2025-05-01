<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Services\UserResourceForm;
use App\Filament\Resources\UserResource\Services\UserResourceInfoList;
use App\Filament\Resources\UserResource\Services\UserResourceTable;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return UserResourceForm::form($form);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return UserResourceTable::table($table);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function infolist(Infolist $infolist): Infolist
    {
        return UserResourceInfoList::infolist($infolist);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            //'create' => Pages\CreateUser::route('/create'),
            //'view' => Pages\ViewUser::route('/{record}'),
            'show' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
