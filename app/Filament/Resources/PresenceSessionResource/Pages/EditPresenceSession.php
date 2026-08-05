<?php

namespace App\Filament\Resources\PresenceSessionResource\Pages;

use App\Filament\Resources\PresenceSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPresenceSession extends EditRecord
{
    protected static string $resource = PresenceSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            PresenceSessionResource\Widgets\AbsentStudentsWidget::class,
        ];
    }
}
