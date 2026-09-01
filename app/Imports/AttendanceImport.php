<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Group;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AttendanceImport implements SkipsOnError, SkipsOnFailure, ToCollection, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importedCount = 0;

    protected $skippedCount = 0;

    public function collection(Collection $collection)
    {
        $availableGroups = Group::with('mentors')->orderBy('order')->get();
        if ($availableGroups->isEmpty()) {
            return;
        }
        $availableMentors = [];
        foreach ($availableGroups as $group) {
            foreach ($group->mentors as $mentor) {
                $availableMentors[] = [
                    'group_id' => $group->id,
                    'mentor_id' => $mentor->id,
                    'mentor_name' => $mentor->name,
                ];
            }
        }
        if (empty($availableMentors)) {
            return;
        }
        shuffle($availableMentors);
        $mentorIndex = 0;
        foreach ($collection as $index => $row) {
            try {
                if ($index === 0) {
                    continue;
                }
                $col0 = isset($row[0]) ? trim((string) $row[0]) : null;
                $col1 = isset($row[1]) ? trim((string) $row[1]) : null;
                $col2 = isset($row[2]) ? trim((string) $row[2]) : null;
                $col3 = isset($row[3]) ? trim((string) $row[3]) : null;
                $col4 = isset($row[4]) ? trim((string) $row[4]) : null;
                if (! $col0 || ! $col1) {
                    $this->skippedCount++;

                    continue;
                }
                $namaPeserta = $col0;
                $nimPeserta = $col1;
                $phoneNumber = null;
                $fakultasPeserta = null;
                $programStudi = null;
                if ($col4 !== null && $col4 !== '') {
                    $phoneNumber = $col2;
                    $fakultasPeserta = $col3;
                    $programStudi = $col4;
                } elseif (preg_match('/^[0-9+\-\s]{6,20}$/', $col2 ?? '')) {
                    $phoneNumber = $col2;
                    $fakultasPeserta = $col3;
                    $programStudi = $col4;
                } else {
                    $fakultasPeserta = $col2;
                    $programStudi = $col3;
                }
                if ($phoneNumber) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
                    if (str_starts_with($cleanPhone, '628')) {
                        $phoneNumber = '08'.substr($cleanPhone, 3);
                    } elseif (str_starts_with($cleanPhone, '8')) {
                        $phoneNumber = '0'.$cleanPhone;
                    } else {
                        $phoneNumber = $cleanPhone;
                    }
                }
                $existingAttendance = Attendance::where('student_id', $nimPeserta)->first();
                if ($existingAttendance) {
                    if ($phoneNumber && empty($existingAttendance->phone_number)) {
                        $existingAttendance->update(['phone_number' => $phoneNumber]);
                    }
                    $this->skippedCount++;

                    continue;
                }
                $selectedMentor = $availableMentors[$mentorIndex % count($availableMentors)];
                $mentorIndex++;
                $uniqueCode = $this->generateUniqueCode();
                $rawBarcode = json_encode([
                    'nama' => $namaPeserta,
                    'student_id' => $nimPeserta,
                    'fakultas' => $fakultasPeserta,
                    'mentor' => $selectedMentor['mentor_name'],
                ]);
                Attendance::create([
                    'group_id' => $selectedMentor['group_id'],
                    'mentor_id' => $selectedMentor['mentor_id'],
                    'name' => $namaPeserta,
                    'student_id' => $nimPeserta,
                    'phone_number' => $phoneNumber,
                    'faculty' => $fakultasPeserta,
                    'study_program' => $programStudi,
                    'unique_code' => $uniqueCode,
                    'raw_barcode' => $rawBarcode,
                    'status' => 'gagal',
                ]);
                $this->importedCount++;
            } catch (Exception $e) {
                $this->skippedCount++;

                continue;
            }
        }
    }

    private function generateUniqueCode(): string
    {
        do {
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $numbers = '0123456789';
            $characters = $letters.$numbers;
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (Attendance::where('unique_code', $code)->exists());

        return $code;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
