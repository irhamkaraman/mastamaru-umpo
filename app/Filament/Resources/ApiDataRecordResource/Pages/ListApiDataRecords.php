<?php

namespace App\Filament\Resources\ApiDataRecordResource\Pages;

use App\Filament\Resources\ApiDataRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiDataRecords extends ListRecords
{
    protected static string $resource = ApiDataRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
