<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Mentor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AttendanceImport implements ToCollection, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importedCount = 0;
    protected $skippedCount = 0;

    public function __construct()
    {
        // Constructor sederhana tanpa session tracking
    }

    public function collection(Collection $collection)
    {
        // Ambil semua kelompok dan mentor yang tersedia
        $availableGroups = Group::with('mentors')->orderBy('order')->get();

        if ($availableGroups->isEmpty()) {
            return;
        }

        // Buat array mentor yang tersedia dengan distribusi merata
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

        // Shuffle mentor untuk distribusi acak
        shuffle($availableMentors);
        $mentorIndex = 0;

        foreach ($collection as $index => $row) {
            try {
                // Skip header row (baris pertama)
                if ($index === 0) {
                    continue;
                }

                // Akses data berdasarkan indeks kolom (A=0, B=1, C=2, D=3)
                $namaPeserta = isset($row[0]) ? trim($row[0]) : null;
                $nimPeserta = isset($row[1]) ? trim($row[1]) : null;
                $fakultasPeserta = isset($row[2]) ? trim($row[2]) : null;
                $programStudi = isset($row[3]) ? trim($row[3]) : null;

                if (!$namaPeserta || !$nimPeserta) {
                    $this->skippedCount++;
                    continue;
                }

                // Jika fakultas tidak ada, pilih secara random
                if (!$fakultasPeserta) {
                    $faculties = [
                        'Fakultas Teknik',
                        'Fakultas Ekonomi dan Bisnis',
                        'Fakultas Ilmu Sosial dan Politik',
                        'Fakultas Hukum',
                        'Fakultas Pertanian',
                        'Fakultas Kedokteran',
                        'Fakultas Keguruan dan Ilmu Pendidikan',
                        'Fakultas Matematika dan Ilmu Pengetahuan Alam',
                        'Fakultas Peternakan',
                        'Fakultas Kehutanan',
                        'Fakultas Ilmu Kelautan dan Perikanan',
                        'Fakultas Kesehatan Masyarakat',
                        'Fakultas Farmasi',
                        'Fakultas Ilmu Budaya'
                    ];
                    $fakultasPeserta = $faculties[array_rand($faculties)];
                }

                // Jika program studi tidak ada, pilih secara random
                if (!$programStudi) {
                    $studyPrograms = [
                        'Teknik Informatika',
                        'Sistem Informasi',
                        'Teknik Elektro',
                        'Manajemen',
                        'Akuntansi',
                        'Ilmu Komunikasi',
                        'Hukum',
                        'Agroteknologi',
                        'Kedokteran',
                        'Pendidikan Bahasa Indonesia'
                    ];
                    $programStudi = $studyPrograms[array_rand($studyPrograms)];
                }

                // Cek apakah peserta dengan NIM ini sudah ada
                $existingAttendance = Attendance::where('student_id', $nimPeserta)->first();
                if ($existingAttendance) {
                    $this->skippedCount++;
                    continue;
                }

                // Pilih mentor secara berurutan untuk distribusi merata
                $selectedMentor = $availableMentors[$mentorIndex % count($availableMentors)];
                $mentorIndex++;

                // Generate unique code untuk peserta
                $uniqueCode = $this->generateUniqueCode();

                // Buat raw_barcode dengan data peserta yang benar
                $rawBarcode = json_encode([
                    'nama' => $namaPeserta,
                    'student_id' => $nimPeserta,
                    'fakultas' => $fakultasPeserta,
                    'mentor' => $selectedMentor['mentor_name']
                ]);

                // Buat peserta baru
                Attendance::create([
                    'group_id' => $selectedMentor['group_id'],
                    'mentor_id' => $selectedMentor['mentor_id'],
                    'name' => $namaPeserta,
                    'student_id' => $nimPeserta,
                    'faculty' => $fakultasPeserta,
                    'study_program' => $programStudi,
                    'unique_code' => $uniqueCode,
                    'raw_barcode' => $rawBarcode,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                continue;
            }
        }
    }

    private function generateUniqueCode(): string
    {
        do {
            // Generate kode dengan kombinasi huruf besar dan angka (8 karakter)
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $numbers = '0123456789';
            $characters = $letters . $numbers;

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
        return 50; // Kurangi batch size untuk update progress yang lebih sering
    }

    public function chunkSize(): int
    {
        return 50; // Kurangi chunk size untuk update progress yang lebih sering
    }
}
