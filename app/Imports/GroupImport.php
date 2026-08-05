<?php

namespace App\Imports;

use App\Models\Group;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class GroupImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cek apakah baris kosong (nama_kelompok kosong atau null)
        if (empty($row['nama_kelompok']) || is_null($row['nama_kelompok'])) {
            return null;
        }

        // Cek apakah nama kelompok sudah ada
        $existingGroup = Group::where('name', $row['nama_kelompok'])->first();
        if ($existingGroup) {
            // Jika sudah ada, update data yang ada
            $existingGroup->update([
                'order' => $row['urutan'] ?? $existingGroup->order,
            ]);
            return null; // Tidak membuat record baru
        }

        // Generate slug dari nama kelompok dengan tetap mempertahankan angka
        $slug = Str::slug($row['nama_kelompok']);
        
        // Pastikan slug tidak kosong
        if (empty($slug)) {
            $slug = 'kelompok';
        }

        // Pastikan slug unik dengan kombinasi huruf acak jika diperlukan
        $originalSlug = $slug;
        $counter = 1;
        while (Group::where('slug', $slug)->exists()) {
            // Jika sudah ada, tambahkan 3 huruf acak
            $randomString = strtolower(Str::random(3));
            $slug = $originalSlug . '-' . $randomString;
            $counter++;
            
            // Fallback jika masih konflik setelah beberapa kali percobaan
            if ($counter > 10) {
                $slug = $originalSlug . '-' . time() . '-' . $randomString;
                break;
            }
        }

        return new Group([
            'name' => $row['nama_kelompok'],
            'slug' => $slug,
            'order' => $row['urutan'] ?? 0,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_kelompok' => 'nullable|string|max:255',
            'urutan' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
            'nama_kelompok.string' => 'Nama kelompok harus berupa teks.',
            'nama_kelompok.max' => 'Nama kelompok maksimal 255 karakter.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.numeric' => 'Urutan harus berupa angka.',
            'urutan.min' => 'Urutan minimal 0.',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
