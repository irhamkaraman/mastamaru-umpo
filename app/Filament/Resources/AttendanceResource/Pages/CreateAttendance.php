<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Mentor;
use App\Models\Attendance;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate unique code (8 karakter huruf besar dan angka)
        do {
            $uniqueCode = strtoupper(Str::random(8));
            // Pastikan mengandung kombinasi huruf dan angka
            $uniqueCode = preg_replace('/[^A-Z0-9]/', '', $uniqueCode);
            if (strlen($uniqueCode) < 8) {
                $uniqueCode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
            }
        } while (Attendance::where('unique_code', $uniqueCode)->exists());
        
        $data['unique_code'] = $uniqueCode;
        
        // Generate raw barcode JSON
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
