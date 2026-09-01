<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UmpoSyncProgressController extends Controller
{
    private $progressKey = 'umpo_sync_progress_status';

    /**
     * Memulai proses sinkronisasi dan menginisialisasi state di cache
     */
    public function startSync(Request $request)
    {
        $initialStatus = [
            'status' => 'fetching_api',
            'step_text' => 'Menghubungi API UMPO (Jurusan & Fakultas)...',
            'percentage' => 5,
            'total_api' => 0,
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'current_student' => '',
            'message' => 'Sedang menarik data dari server UMPO...',
            'started_at' => microtime(true),
            'last_update' => now()->toTimeString(),
        ];
        Cache::put($this->progressKey, $initialStatus, 600);

        return response()->json([
            'success' => true,
            'message' => 'Proses sinkronisasi dimulai.',
        ]);
    }

    /**
     * Mengambil data progres saat ini secara realtime (per detik)
     */
    public function getProgress()
    {
        $status = Cache::get($this->progressKey, [
            'status' => 'idle',
            'step_text' => 'Siap untuk sinkronisasi',
            'percentage' => 0,
            'total_api' => 0,
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'current_student' => '',
            'message' => 'Belum ada proses yang berjalan',
            'elapsed_seconds' => 0,
        ]);
        if (isset($status['started_at'])) {
            $status['elapsed_seconds'] = round(microtime(true) - $status['started_at'], 1);
        }

        return response()->json($status);
    }

    /**
     * Menjalankan proses batch chunk sync
     */
    public function executeBatch(Request $request)
    {
        try {
            $jurusanUrl = 'https://apikey.umpo.ac.id/api/jurusan/find-All';
            $jurusanResponse = Http::timeout(30)->get($jurusanUrl);
            $jurusanData = $jurusanResponse->successful() ? ($jurusanResponse->json('data') ?? []) : [];
            $fakultasUrl = 'https://apikey.umpo.ac.id/api/fakultas/find-all';
            $fakultasResponse = Http::timeout(30)->get($fakultasUrl);
            $fakultasDataApi = $fakultasResponse->successful() ? ($fakultasResponse->json('data') ?? []) : [];
            $fakultasDict = [];
            foreach ($fakultasDataApi as $f) {
                if (isset($f['kodeFakultas']) && isset($f['namaFakultas'])) {
                    $fakultasDict[$f['kodeFakultas']] = $f['namaFakultas'];
                }
            }
            $jurusanDict = [];
            foreach ($jurusanData as $j) {
                $kodeFak = $j['kodeFakultas'] ?? '';
                $kodeJur = $j['kodeJurusan'] ?? '';
                $key = $kodeFak.'-'.$kodeJur;
                $jurusanDict[$key] = $j['programStudi'] ?? $j['namaJurusan'] ?? '';
            }
            Cache::put($this->progressKey, array_merge(Cache::get($this->progressKey, []), [
                'step_text' => 'Mengambil token otentikasi API UMPO...',
                'percentage' => 10,
            ]), 600);
            $accesscode = 'd6e2ec2be6d9527a21f034e1bee325b5ce4d2154cb0475943f1880c3fcbcee11';
            $tokenUrl = 'https://apikey.umpo.ac.id/generate-token?'.http_build_query([
                'apiLink' => 'http://76.76.76.185:8088/api-key/mahasiswas/find-all',
                'accesscodeTalker' => $accesscode,
            ]);
            $tokenResponse = Http::timeout(15)
                ->withoutVerifying()
                ->withHeaders(['Accept' => 'application/json'])
                ->post($tokenUrl);
            $authToken = null;
            if ($tokenResponse->successful()) {
                $tokenData = $tokenResponse->json();
                $authToken = $tokenData['token'] ?? null;
            }
            if (! $authToken) {
                throw new Exception('Gagal mendapatkan token otentikasi API UMPO.');
            }
            Cache::put($this->progressKey, array_merge(Cache::get($this->progressKey, []), [
                'step_text' => 'Mengambil daftar mahasiswa tahun 2026 dari API UMPO...',
                'percentage' => 15,
            ]), 600);
            $mhsUrl = 'https://apikey.umpo.ac.id/api-key/mahasiswas/find-all?tahun=2026';
            $mhsResponse = Http::timeout(60)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ])
                ->get($mhsUrl);
            if (! $mhsResponse->successful()) {
                throw new Exception('Gagal menghubungi API Mahasiswa: HTTP '.$mhsResponse->status().' - '.$mhsResponse->body());
            }
            $mhsData = $mhsResponse->json('data') ?? [];
            $totalMhs = count($mhsData);
            if ($totalMhs === 0) {
                Cache::put($this->progressKey, [
                    'status' => 'completed',
                    'step_text' => 'Selesai (Tidak ada data 2026)',
                    'percentage' => 100,
                    'total_api' => 0,
                    'processed' => 0,
                    'created' => 0,
                    'updated' => 0,
                    'message' => 'API tidak mengembalikan data mahasiswa untuk tahun 2026.',
                ], 600);

                return response()->json(['success' => true, 'total' => 0]);
            }
            $existingStudents = Attendance::pluck('id', 'student_id')->toArray();
            $existingUniqueCodes = Attendance::pluck('unique_code')->filter()->flip()->toArray();
            $totalCreated = 0;
            $totalUpdated = 0;
            $processedCount = 0;
            $chunks = array_chunk($mhsData, 250);
            $totalChunks = count($chunks);
            foreach ($chunks as $chunkIndex => $chunk) {
                $upsertData = [];
                $lastStudentName = '';
                foreach ($chunk as $mhs) {
                    $nim = trim($mhs['nim'] ?? '');
                    if (empty($nim)) {
                        continue;
                    }
                    $kodeFak = $mhs['kodeFakultas'] ?? '';
                    $kodeJur = $mhs['kodeJurusan'] ?? '';
                    $dictKey = $kodeFak.'-'.$kodeJur;
                    $programStudi = $jurusanDict[$dictKey] ?? $kodeJur;
                    $namaFakultas = $fakultasDict[$kodeFak] ?? $kodeFak;
                    $namaMhs = trim($mhs['namaMhs'] ?? 'Mahasiswa');
                    $telepon = $mhs['teleponMhs'] ?? $mhs['telepon'] ?? $mhs['phone'] ?? null;
                    $lastStudentName = $namaMhs.' ('.$nim.')';
                    $isExisting = isset($existingStudents[$nim]);
                    if ($isExisting) {
                        $totalUpdated++;
                    } else {
                        $totalCreated++;
                    }
                    $uniqueCode = Str::upper(Str::random(8));
                    while (isset($existingUniqueCodes[$uniqueCode])) {
                        $uniqueCode = Str::upper(Str::random(8));
                    }
                    $existingUniqueCodes[$uniqueCode] = true;
                    $upsertData[] = [
                        'student_id' => $nim,
                        'name' => $namaMhs,
                        'study_program' => $programStudi,
                        'faculty' => $namaFakultas,
                        'phone_number' => $telepon,
                        'unique_code' => $uniqueCode,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $processedCount++;
                }
                if (! empty($upsertData)) {
                    Attendance::upsert(
                        $upsertData,
                        ['student_id'],
                        ['name', 'study_program', 'faculty', 'phone_number', 'updated_at']
                    );
                }
                $pct = round(15 + (($processedCount / $totalMhs) * 85));
                if ($pct > 99) {
                    $pct = 99;
                }
                Cache::put($this->progressKey, [
                    'status' => 'processing',
                    'step_text' => 'Menyimpan batch data ke database ('.($chunkIndex + 1)."/{$totalChunks})...",
                    'percentage' => $pct,
                    'total_api' => $totalMhs,
                    'processed' => $processedCount,
                    'created' => $totalCreated,
                    'updated' => $totalUpdated,
                    'current_student' => $lastStudentName,
                    'message' => "Memproses {$processedCount} dari {$totalMhs} data mahasiswa...",
                    'started_at' => Cache::get($this->progressKey)['started_at'] ?? microtime(true),
                    'last_update' => now()->toTimeString(),
                ], 600);
            }
            Cache::put($this->progressKey, [
                'status' => 'completed',
                'step_text' => 'Sinkronisasi Selesai!',
                'percentage' => 100,
                'total_api' => $totalMhs,
                'processed' => $processedCount,
                'created' => $totalCreated,
                'updated' => $totalUpdated,
                'current_student' => 'Semua data telah selesai disinkronkan.',
                'message' => "Sukses! Berhasil menyinkronkan {$processedCount} data mahasiswa ({$totalCreated} data baru, {$totalUpdated} data diperbarui).",
                'last_update' => now()->toTimeString(),
            ], 600);

            return response()->json([
                'success' => true,
                'total_api' => $totalMhs,
                'processed' => $processedCount,
                'created' => $totalCreated,
                'updated' => $totalUpdated,
            ]);
        } catch (Exception $e) {
            Cache::put($this->progressKey, [
                'status' => 'error',
                'step_text' => 'Terjadi Kesalahan!',
                'percentage' => 100,
                'message' => $e->getMessage(),
            ], 600);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
