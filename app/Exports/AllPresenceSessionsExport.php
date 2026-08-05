<?php

namespace App\Exports;

use App\Models\PresenceSession;
use App\Models\AttendanceSubmission;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AllPresenceSessionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    /**
     * Mengambil semua data presensi dari semua sesi
     */
    public function collection()
    {
        $allData = collect();
        
        // Ambil semua sesi presensi
        $sessions = PresenceSession::orderBy('created_at', 'desc')->get();
        
        foreach ($sessions as $session) {
            // Ambil semua peserta yang terdaftar
            $allStudents = Attendance::with(['group', 'mentor'])->get();
            
            foreach ($allStudents as $student) {
                // Cek apakah peserta sudah presensi di sesi ini
                $submission = AttendanceSubmission::where('presence_session_id', $session->id)
                    ->where('student_id', $student->id)
                    ->first();
                
                $allData->push([
                    'session' => $session,
                    'student' => $student,
                    'submission' => $submission
                ]);
            }
        }
        
        return $allData;
    }

    /**
     * Header kolom untuk file Excel
     */
    public function headings(): array
    {
        return [
            'Nama Sesi Presensi',
            'Kode Sesi',
            'Waktu Mulai',
            'Waktu Selesai',
            'Nama Peserta',
            'NIM/ID Peserta',
            'Kelompok',
            'Pendamping',
            'Fakultas',
            'Program Studi',
            'Status Kehadiran',
            'Waktu Presensi',
            'Metode Presensi',
            'Catatan'
        ];
    }

    /**
     * Mapping data untuk setiap baris
     */
    public function map($row): array
    {
        $session = $row['session'];
        $student = $row['student'];
        $submission = $row['submission'];
        
        return [
            $session->session_name,
            $session->session_code,
            \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i'),
            \Carbon\Carbon::parse($session->end_time)->format('d/m/Y H:i'),
            $student->name,
            $student->student_id,
            $student->group->name ?? 'Tidak ada kelompok',
            $student->mentor->name ?? 'Tidak ada pendamping',
            $student->faculty ?? 'Tidak tersedia',
            $student->study_program ?? 'Tidak tersedia',
            $submission ? $this->getStatusLabel($submission->status) : 'Tidak Hadir',
            $submission ? \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y H:i:s') : '-',
            $submission ? $this->getMethodLabel($submission->submission_method) : '-',
            $submission->notes ?? '-'
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
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ],
        ];
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
     * Mengubah metode presensi menjadi label yang mudah dibaca
     */
    private function getMethodLabel($method): string
    {
        switch ($method) {
            case 'qr_code':
                return 'QR Code';
            case 'manual':
                return 'Manual';
            case 'barcode':
                return 'Barcode';
            case 'manual_mentor':
                return 'Manual oleh Mentor';
            default:
                return 'Manual';
        }
    }
}