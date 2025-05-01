<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscordUserResource\Pages;
use App\Filament\Resources\DiscordUserResource\Pages\ViewDiscordUser;
use App\Filament\Resources\DiscordUserResource\RelationManagers\RemaindersRelationManager;
use App\Filament\Resources\DiscordUserResource\Services\DiscordUserResourceForm;
use App\Filament\Resources\DiscordUserResource\Services\DiscordUserResourceInfoList;
use App\Filament\Resources\DiscordUserResource\Services\DiscordUserResourceTable;
use App\Models\DiscordUser;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Number;

class DiscordUserResource extends Resource
{
    //---------------------------------------------------------------------------------------------------------------
    protected static ?string $model = DiscordUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Backend';

    protected static ?string $navigationBadgeTooltip = 'Total Discord Users count';


    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('View')
                ->url(ViewDiscordUser::getUrl(['record' => $record])),
            Action::make('Remainders')
                ->url(DiscordUserResourceTable::getRemainderActionUrl($record)),
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record]), shouldOpenInNewTab: false),
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return "{$record->global_name} ({$record->user_name})";
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return DiscordUserResource::getUrl('show', ['record' => $record]);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'user_name',
            'global_name',
            'snowflake',
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $result = [];

        if ($record->trashed()) {
            $result['deleted'] = $record->deleted_at;
        }
        $result['remainders'] = $record->remainders_count;
        return $result;
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->withCount('remainders');
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function infolist(Infolist $infolist): Infolist
    {
        return DiscordUserResourceInfoList::infolist($infolist);
    }


    //---------------------------------------------------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return DiscordUserResourceForm::form($form);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return DiscordUserResourceTable::table($table);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getRelations(): array
    {
        return [
            RemaindersRelationManager::class,
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscordUsers::route('/'),
            'show' => Pages\ViewDiscordUser::route('/{record}'), // replaces default view
            'edit' => Pages\EditDiscordUser::route('/{record}/edit'),
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
        ;
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getNavigationBadge(): ?string
    {
        //NOTE: this gets called twice per page load, (see calls bellow), so we cache it for better performance
        //      GET	    /admin/discord-users
        //      POST	/livewire/update (ajax)
        //NOTE: this could be cached for a longer time, but then the cache had to be invalidated manually on DU count change...

        return Number::format(
            cache()->remember(
                key: 'DiscordUserResourceBadgeCount',
                ttl: 10,
                callback: fn () => static::getModel()::count()
            )
        );
    }
}
