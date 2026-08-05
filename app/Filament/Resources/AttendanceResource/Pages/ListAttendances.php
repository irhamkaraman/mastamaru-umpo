<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    
    protected static string $view = 'filament.resources.attendance-resource.pages.list-attendances';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->can('create_attendance') ?? false), /** @phpstan-ignore-line */
        ];
    }
    
    public function getListeners(): array
    {
        return [
            'start-import-progress' => 'handleImportProgress',
        ];
    }
    
    public function handleImportProgress($sessionId)
    {
        $this->dispatch('start-import-progress', sessionId: $sessionId);
    }
}
