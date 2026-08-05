<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Mentor;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update raw barcode JSON saat edit
        $mentor = Mentor::find($data['mentor_id']);
        $rawBarcode = json_encode([
            'nama' => $data['name'],
            'student_id' => $data['student_id'],
            'mentor' => $mentor ? $mentor->name : ''
        ]);
        
        $data['raw_barcode'] = $rawBarcode;
        
        return $data;
    }
}
