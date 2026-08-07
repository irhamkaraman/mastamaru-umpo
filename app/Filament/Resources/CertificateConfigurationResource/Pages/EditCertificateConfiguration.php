<?php

namespace App\Filament\Resources\CertificateConfigurationResource\Pages;

use App\Filament\Resources\CertificateConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificateConfiguration extends EditRecord
{
    protected static string $resource = CertificateConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
