<?php

namespace App\Filament\Resources\PresenceSessionResource\Pages;

use App\Filament\Resources\PresenceSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresenceSessions extends ListRecords
{
    protected static string $resource = PresenceSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
