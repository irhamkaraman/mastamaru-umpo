<?php

namespace App\Filament\Resources\CertificateConfigurationResource\Pages;

use App\Filament\Resources\CertificateConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificateConfigurations extends ListRecords
{
    protected static string $resource = CertificateConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
