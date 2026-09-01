<?php

namespace App\Imports;

use App\Models\Group;
use App\Models\Mentor;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MentorImport implements SkipsOnError, SkipsOnFailure, ToCollection, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importedCount = 0;

    protected $skippedCount = 0;

    public function collection(Collection $collection)
    {
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
                if (! $col0 || ! $col1 || ! $col2) {
                    $this->skippedCount++;

                    continue;
                }
                $namaKelompok = $col0;
                $namaPendamping = $col1;
                $nim = $col2;
                $phoneNumber = null;
                $kataSandi = null;
                if ($col4 !== null && $col4 !== '') {
                    $phoneNumber = $col3;
                    $kataSandi = $col4;
                } elseif (preg_match('/^[0-9+\-\s]{6,20}$/', $col3 ?? '')) {
                    $phoneNumber = $col3;
                    $kataSandi = 'password123';
                } else {
                    $kataSandi = $col3 ?? 'password123';
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
                $group = Group::where('name', $namaKelompok)->first();
                if (! $group) {
                    $group = Group::create([
                        'name' => $namaKelompok,
                        'order' => Group::count() + 1,
                    ]);
                }
                $existingMentor = Mentor::where('student_id', $nim)->first();
                if ($existingMentor) {
                    $updateData = [];
                    if ($phoneNumber) {
                        $updateData['phone_number'] = $phoneNumber;
                    }
                    if ($group) {
                        $updateData['group_id'] = $group->id;
                    }
                    if (! empty($updateData)) {
                        $existingMentor->update($updateData);
                    }
                    $this->skippedCount++;

                    continue;
                }
                Mentor::create([
                    'group_id' => $group->id,
                    'name' => $namaPendamping,
                    'student_id' => $nim,
                    'phone_number' => $phoneNumber,
                    'password' => Hash::make($kataSandi),
                    'raw_password' => $kataSandi,
                ]);
                $this->importedCount++;
            } catch (Exception $e) {
                $this->skippedCount++;

                continue;
            }
        }
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
