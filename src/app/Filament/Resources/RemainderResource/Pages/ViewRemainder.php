<?php

namespace App\Filament\Resources\RemainderResource\Pages;

use App\Filament\Resources\RemainderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRemainder extends ViewRecord
{
    protected static string $resource = RemainderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
