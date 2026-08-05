<?php

namespace App\Filament\Resources\ApiConfigurationResource\Pages;

use App\Filament\Resources\ApiConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiConfiguration extends EditRecord
{
    protected static string $resource = ApiConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
