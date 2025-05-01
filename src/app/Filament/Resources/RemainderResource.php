<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RemainderResource\Pages;
use App\Filament\Resources\RemainderResource\Sections\RemainderResourceForm;
use App\Filament\Resources\RemainderResource\Sections\RemainderResourceInfoList;
use App\Filament\Resources\RemainderResource\Sections\RemainderResourceTable;
use App\Models\Remainder;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class RemainderResource extends Resource
{
    //---------------------------------------------------------------------------------------------------------------
    protected static ?string $model = Remainder::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Backend';

    protected static ?string $navigationBadgeTooltip = 'Total Remainders count';

    public static function getGlobalSearchResultActions(Model $record): array
    {
        // identify record
        $parameters = [
            'tableActionRecord' => $record->id,
        ];

        // if trashed, set filter on table to include deleted items
        if ($record->trashed()) {
            $parameters['tableFilters'] = [
                'trashed' => [
                    'value' => 1,
                ],
            ];
        }

        // return the actions
        return [
            Action::make('info')
                ->url(route('filament.admin.resources.remainders.index', $parameters + [
                    'tableAction' => 'info',
                ])),
            Action::make('edit')
                ->url(route('filament.admin.resources.remainders.index', $parameters + [
                    'tableAction' => 'edit',
                ])),
        ];
    }


    //---------------------------------------------------------------------------------------------------------------
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->message;
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'message',
            'channel_id',
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'user' => $record->discordUser->global_name,
            'due at' => $record->due_at,
            'status' => $record->status,
        ];
    }


    //---------------------------------------------------------------------------------------------------------------
    public static function infolist(Infolist $infolist): Infolist
    {
        return RemainderResourceInfoList::infolist($infolist);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function form(Form $form): Form
    {
        return RemainderResourceForm::form($form);
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function table(Table $table): Table
    {
        return RemainderResourceTable::table($table);
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
            'index' => Pages\ListRemainders::route('/'),
            //'create' => Pages\CreateRemainder::route('/create'),
            //'view' => Pages\ViewRemainder::route('/{record}'),
            //'edit' => Pages\EditRemainder::route('/{record}/edit'),
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('discordUser')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
        ;
    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getNavigationBadge(): ?string
    {
        //NOTE: this gets called twice, (see calls bellow), we cache it for better performance
        //      GET	    /admin/discord-users
        //      POST	/livewire/update (ajax)

        return Number::format(
            cache()->remember(
                key: 'RemainderResourceBadgeCount',
                ttl: 10,
                callback: fn () => static::getModel()::count()
            )
        );

    }

    //---------------------------------------------------------------------------------------------------------------
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

}
