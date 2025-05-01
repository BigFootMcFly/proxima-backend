<?php

namespace App\Filament\Resources\DiscordUserResource\Pages;

use App\Filament\Resources\DiscordUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscordUsers extends ListRecords
{
    protected static string $resource = DiscordUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
