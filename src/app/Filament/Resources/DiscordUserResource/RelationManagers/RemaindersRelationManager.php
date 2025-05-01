<?php

namespace App\Filament\Resources\DiscordUserResource\RelationManagers;

use App\Filament\Resources\RemainderResource;
use App\Filament\Resources\RemainderResource\Sections\RemainderResourceInfoList;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class RemaindersRelationManager extends RelationManager
{

    protected static ?string $icon = 'heroicon-o-calendar';

    protected static ?string $iconColor = 'red';

    protected static string $relationship = 'remainders';

    public function form(Form $form): Form
    {
        return RemainderResource::form($form);
    }

    public function table(Table $table): Table
    {
        // add custom icon to the related section to match the design of the main section
        $heading = new HtmlString(
            html: view(
                view: 'filament.admin.relation-manager-section-icon',
                data: [
                    'icon' => self::$icon,
                    'content' => $table->getheading(),
                ]
            )
        );

        return RemainderResource::table($table)
            ->description('The remainders of the discord user.')
            ->heading($heading)
        ;
    }

    public function applyFiltersToTableQuery(Builder $query): Builder
    {
        // return the record even if it is trashed
        if ($this->ownerRecord->trashed()) {
            return $query->withTrashed();
        }

        return $query;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        // return our own infolist instead of the default one
        return RemainderResourceInfoList::infolist($infolist);
    }
}
