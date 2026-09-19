<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\ScoreCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateApiController extends Controller
{
    public function pending()
    {
        $pending = Attendance::with('group')
            ->whereNull('certificate_file')
            ->orWhere('certificate_file', '')
            ->get();
            
        $data = $pending->map(function ($item) {
            return [
                'id' => $item->student_id,
                'name' => $item->name,
                'group_name' => $item->group ? $item->group->name : 'Tanpa Kelompok'
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function participant($id)
    {
        $attendance = Attendance::with(['group.mentors', 'assessment', 'attendanceSubmissions.presenceSession'])->where('student_id', $id)->first();
        if (!$attendance) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $grouped = $attendance->attendanceSubmissions->groupBy(function($sub) {
            return $sub->presenceSession->day_number ?? 1;
        });

        $riwayat = $grouped->map(function ($subs, $day) {
            $dates = $subs->map(function($sub) {
                return $sub->submitted_at ? $sub->submitted_at->format('d M Y') : null;
            })->filter();

            $date = '-';
            if ($dates->isNotEmpty()) {
                $dateCounts = $dates->countBy();
                $date = $dateCounts->sortDesc()->keys()->first();
            }

            $hari_tanggal = 'Hari ' . $day . "\n" . $date;
            
            $sesiList = $subs->map(function($s) {
                return ucfirst(strtolower($s->presenceSession->session_type ?? 'Materi'));
            })->unique()->implode(', ');
            
            $sesi_kegiatan = "Seluruh Rangkaian Kegiatan\n(" . $sesiList . ")";
            
            $status = $subs->every(fn($s) => strtolower($s->status) == 'hadir') ? 'Hadir Penuh' : 'Hadir Sebagian';
            $firstSub = $subs->first();
            $pemandu = $firstSub->mentor ? $firstSub->mentor->name : 'Sistem';
            $poin = '+' . $subs->sum('score_points') . 'p';

            return [
                'hari_tanggal' => $hari_tanggal,
                'sesi_kegiatan' => $sesi_kegiatan,
                'status' => $status,
                'pemandu' => $pemandu,
                'poin' => $poin
            ];
        })->values()->toArray();

        $total_hari_hadir = $grouped->count();
        $status_kelulusan = ($total_hari_hadir <= 1) ? 'GAGAL' : 'LULUS';
        $assessment = ScoreCalculationService::recalculateForStudent($attendance->id);
        
        $total_poin = $assessment ? $assessment->final_score : 100;
        $predikat = $assessment ? $assessment->grade : 'A';

        $data = [
            'id' => $attendance->student_id,
            'raw_id' => $attendance->id,
            'name' => strtoupper($attendance->name),
            'nama' => strtoupper($attendance->name),
            'nim' => $attendance->student_id,
            'kelompok' => $attendance->group ? strtoupper($attendance->group->name) : '-',
            'pendamping' => $attendance->group && $attendance->group->mentors->isNotEmpty() ? $attendance->group->mentors->first()->name : '-',
            'fakultas' => strtoupper($attendance->faculty ?? '-'),
            'prodi' => strtoupper($attendance->study_program ?? '-'),
            'status' => $attendance->assessment ? strtoupper($attendance->assessment->status) : 'LULUS',
            'nilai_akhir' => $attendance->assessment ? $attendance->assessment->final_score : '100',
            'grade' => $attendance->assessment ? $attendance->assessment->grade : 'A',
            'status_kelulusan' => $status_kelulusan,
            'total_nilai' => $total_poin,
            'predikat_kelulusan' => $predikat,
            'riwayat_presensi' => $riwayat
        ];

        return response()->json($data);
    }

    public function upload(Request $request, $id)
    {
        $request->validate([
            'certificate_pdf' => 'required|file|mimes:pdf'
        ]);

        $attendance = Attendance::where('student_id', $id)->first();
        if (!$attendance) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $file = $request->file('certificate_pdf');
        
        $slugName = Str::slug($attendance->name, '_');
        $filename = "{$attendance->student_id}_sertifikat_{$slugName}.pdf";
        
        $path = $file->storeAs('certificates', $filename, 'public');
        
        $attendance->update([
            'certificate_file' => $path,
            'status' => 'lulus'
        ]);

        \App\Models\StudentAssessment::updateOrCreate(
            ['student_id' => $attendance->id],
            [
                'final_score' => 100,
                'grade' => 'A',
                'status' => 'lulus'
            ]
        );

        return response()->json(['message' => 'Uploaded successfully', 'path' => $path]);
    }
}