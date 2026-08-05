<?php

namespace App\Imports;

use App\Models\Mentor;
use App\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class MentorImport implements ToCollection, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importedCount = 0;
    protected $skippedCount = 0;

    public function collection(Collection $collection)
    {
        foreach ($collection as $index => $row) {
            try {
                // Skip header row (baris pertama)
                if ($index === 0) {
                    continue;
                }

                // Akses data berdasarkan indeks kolom (A=0, B=1, C=2, D=3)
                $namaKelompok = isset($row[0]) ? trim($row[0]) : null;
                $namaPendamping = isset($row[1]) ? trim($row[1]) : null;
                $nim = isset($row[2]) ? trim($row[2]) : null;
                $kataSandi = isset($row[3]) ? trim($row[3]) : null;

                if (!$namaKelompok || !$namaPendamping || !$nim || !$kataSandi) {
                    $this->skippedCount++;
                    continue;
                }

                // Cari group berdasarkan nama
                $group = Group::where('name', $namaKelompok)->first();

                if (!$group) {
                    $this->skippedCount++;
                    continue;
                }

                // Cek apakah mentor dengan NIM ini sudah ada
                $existingMentor = Mentor::where('student_id', $nim)->first();
                if ($existingMentor) {
                    $this->skippedCount++;
                    continue;
                }

                // Buat mentor baru
                Mentor::create([
                    'group_id' => $group->id,
                    'name' => $namaPendamping,
                    'student_id' => $nim,
                    'password' => Hash::make($kataSandi),
                    'raw_password' => $kataSandi,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                continue;
            }
        }
    }

    public function rules(): array
    {
        // Validasi dilakukan secara manual di method collection
        return [];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
            'nama_pendamping.required' => 'Nama pendamping wajib diisi.',
            'nama_pendamping.max' => 'Nama pendamping maksimal 255 karakter.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'nim.max' => 'NIM maksimal 255 karakter.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
        ];
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
