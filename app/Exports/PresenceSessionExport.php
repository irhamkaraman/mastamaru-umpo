<?php

namespace App\Exports;

use App\Models\PresenceSession;
use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PresenceSessionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, WithTitle
{
    protected $presenceSession;

    public function __construct(PresenceSession $presenceSession)
    {
        $this->presenceSession = $presenceSession;
    }

    /**
     * Mengambil data untuk export
     */
    public function collection(): Collection
    {
        // Ambil semua peserta (Attendance) dengan relasi
        $allStudents = Attendance::with(['group', 'mentor'])->get();
        
        // Ambil data submission untuk sesi ini dengan relasi
        $submissions = AttendanceSubmission::where('presence_session_id', $this->presenceSession->id)
            ->with(['student.group', 'student.mentor'])
            ->get()
            ->keyBy('student_id');

        // Gabungkan data peserta dengan status kehadiran
        return $allStudents->map(function ($student) use ($submissions) {
            $submission = $submissions->get($student->id);
            
            return (object) [
                'student' => $student,
                'submission' => $submission,
                'status' => $submission ? $submission->status : 'tidak_hadir',
                'submitted_at' => $submission ? $submission->submitted_at : null,
                'submission_method' => $submission ? $submission->submission_method : null,
                'notes' => $submission ? $submission->notes : 'Tidak melakukan presensi'
            ];
        });
    }

    /**
     * Mapping data untuk setiap row
     */
    public function map($row): array
    {
        return [
            $row->student->name,
            $row->student->student_id,
            $row->student->faculty ?? 'Tidak tersedia',
            $row->student->study_program ?? 'Tidak tersedia',
            $row->student->group ? $row->student->group->name : 'Tidak tersedia',
            $row->student->mentor ? $row->student->mentor->name : 'Tidak tersedia',
            $this->getStatusLabel($row->status),
            $row->submitted_at ? $row->submitted_at->format('d/m/Y H:i:s') : '-',
            $this->getSubmissionMethodLabel($row->submission_method),
            $row->notes ?? '-'
        ];
    }

    /**
     * Header kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'Nama Peserta',
            'NIM',
            'Fakultas',
            'Program Studi',
            'Kelompok',
            'Pendamping',
            'Status Kehadiran',
            'Waktu Presensi',
            'Metode Presensi',
            'Catatan'
        ];
    }

    /**
     * Styling untuk worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

    /**
     * Title untuk worksheet
     */
    public function title(): string
    {
        return 'Data Presensi - ' . $this->presenceSession->session_name;
    }

    /**
     * Mengubah status menjadi label yang mudah dibaca
     */
    private function getStatusLabel($status): string
    {
        switch ($status) {
            case 'hadir':
                return 'Hadir';
            case 'terlambat':
                return 'Terlambat';
            case 'tidak_hadir':
                return 'Tidak Hadir';
            case 'izin':
                return 'Izin';
            case 'sakit':
                return 'Sakit';
            default:
                return 'Tidak Hadir';
        }
    }

    /**
     * Mengubah metode submission menjadi label yang mudah dibaca
     */
    private function getSubmissionMethodLabel($method): string
    {
        switch ($method) {
            case 'qr_scan':
                return 'Scan QR Code';
            case 'manual_mentor':
                return 'Input Manual Mentor';
            case 'barcode_scan':
                return 'Scan Barcode';
            default:
                return '-';
        }
    }
}