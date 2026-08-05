<?php

namespace App\Filament\Resources\ApiDataRecordResource\Pages;

use App\Filament\Resources\ApiDataRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiDataRecord extends EditRecord
{
    protected static string $resource = ApiDataRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
