<?php

namespace App\Imports;

use App\Models\Group;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GroupImport implements SkipsOnError, SkipsOnFailure, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (empty($row['nama_kelompok']) || is_null($row['nama_kelompok'])) {
            return null;
        }
        $existingGroup = Group::where('name', $row['nama_kelompok'])->first();
        if ($existingGroup) {
            $existingGroup->update([
                'order' => $row['urutan'] ?? $existingGroup->order,
            ]);

            return null;
        }
        $slug = Str::slug($row['nama_kelompok']);
        if (empty($slug)) {
            $slug = 'kelompok';
        }
        $originalSlug = $slug;
        $counter = 1;
        while (Group::where('slug', $slug)->exists()) {
            $randomString = strtolower(Str::random(3));
            $slug = $originalSlug.'-'.$randomString;
            $counter++;
            if ($counter > 10) {
                $slug = $originalSlug.'-'.time().'-'.$randomString;
                break;
            }
        }

        return new Group([
            'name' => $row['nama_kelompok'],
            'slug' => $slug,
            'order' => $row['urutan'] ?? 0,
        ]);
    }

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

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
