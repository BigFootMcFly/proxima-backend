<?php

namespace App\Filament\Resources\RemainderResource\Pages;

use App\Filament\Resources\RemainderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemainder extends EditRecord
{

    protected static string $resource = RemainderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

}
