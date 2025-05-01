<?php

namespace App\Filament\Resources\DiscordUserResource\Pages;

use App\Filament\Resources\DiscordUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscordUser extends ViewRecord
{
    protected static string $resource = DiscordUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
