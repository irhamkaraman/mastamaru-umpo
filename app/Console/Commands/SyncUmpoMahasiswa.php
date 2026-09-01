<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncUmpoMahasiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'umpo:sync-mahasiswa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active students from UMPO API and translate their majors.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching Data Jurusan dari API UMPO...');
        $jurusanUrl = 'https://apikey.umpo.ac.id/api/jurusan/find-All';
        try {
            $jurusanResponse = Http::timeout(30)->get($jurusanUrl);
            if (! $jurusanResponse->successful()) {
                $this->error('Gagal mengambil data Jurusan: HTTP '.$jurusanResponse->status());

                return Command::FAILURE;
            }
            $jurusanData = $jurusanResponse->json('data') ?? [];
        } catch (Exception $e) {
            $this->error('Error koneksi API Jurusan: '.$e->getMessage());

            return Command::FAILURE;
        }
        $this->info('Fetching Data Fakultas dari API UMPO...');
        $fakultasUrl = 'https://apikey.umpo.ac.id/api/fakultas/find-all';
        try {
            $fakultasResponse = Http::timeout(30)->get($fakultasUrl);
            if (! $fakultasResponse->successful()) {
                $this->error('Gagal mengambil data Fakultas: HTTP '.$fakultasResponse->status());

                return Command::FAILURE;
            }
            $fakultasDataApi = $fakultasResponse->json('data') ?? [];
        } catch (Exception $e) {
            $this->error('Error koneksi API Fakultas: '.$e->getMessage());

            return Command::FAILURE;
        }
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
        $this->info('Mengambil token otentikasi API UMPO...');
        $accesscode = 'd6e2ec2be6d9527a21f034e1bee325b5ce4d2154cb0475943f1880c3fcbcee11';
        $tokenUrl = 'https://apikey.umpo.ac.id/generate-token?'.http_build_query([
            'apiLink' => 'http://76.76.76.185:8088/api-key/mahasiswas/find-all',
            'accesscodeTalker' => $accesscode,
        ]);
        $authToken = null;
        try {
            $tokenResponse = Http::timeout(15)
                ->withoutVerifying()
                ->withHeaders(['Accept' => 'application/json'])
                ->post($tokenUrl);
            if ($tokenResponse->successful()) {
                $tokenData = $tokenResponse->json();
                $authToken = $tokenData['token'] ?? null;
            }
        } catch (Exception $e) {
            $this->error('Error generate token API: '.$e->getMessage());

            return Command::FAILURE;
        }
        if (! $authToken) {
            $this->error('Gagal mendapatkan token otentikasi API UMPO.');

            return Command::FAILURE;
        }
        $this->info('Fetching Data Mahasiswa Tahun 2026 dari API UMPO...');
        $mhsUrl = 'https://apikey.umpo.ac.id/api-key/mahasiswas/find-all?tahun=2026';
        try {
            $mhsResponse = Http::timeout(60)
                ->withoutVerifying()
                ->withHeaders([
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ])
                ->get($mhsUrl);
            if (! $mhsResponse->successful()) {
                $this->error('Gagal mengambil data Mahasiswa: HTTP '.$mhsResponse->status().' - '.$mhsResponse->body());

                return Command::FAILURE;
            }
            $mhsData = $mhsResponse->json('data') ?? [];
        } catch (Exception $e) {
            $this->error('Error koneksi API Mahasiswa: '.$e->getMessage());

            return Command::FAILURE;
        }
        $existingStudents = Attendance::pluck('id', 'student_id')->toArray();
        $existingUniqueCodes = Attendance::pluck('unique_code')->filter()->flip()->toArray();
        $this->info('Ditemukan '.count($mhsData).' mahasiswa dari API Tahun 2026.');
        $this->info('Memproses sinkronisasi super cepat via batch chunking...');
        $bar = $this->output->createProgressBar(count($mhsData));
        $bar->start();
        $chunks = array_chunk($mhsData, 250);
        $countProcessed = 0;
        foreach ($chunks as $chunk) {
            $upsertData = [];
            foreach ($chunk as $mhs) {
                $nim = trim($mhs['nim'] ?? '');
                if (empty($nim)) {
                    $bar->advance();

                    continue;
                }
                $kodeFak = $mhs['kodeFakultas'] ?? '';
                $kodeJur = $mhs['kodeJurusan'] ?? '';
                $dictKey = $kodeFak.'-'.$kodeJur;
                $programStudi = $jurusanDict[$dictKey] ?? $kodeJur;
                $namaFakultas = $fakultasDict[$kodeFak] ?? $kodeFak;
                $phoneNumber = $mhs['teleponMhs'] ?? $mhs['telepon'] ?? $mhs['phone'] ?? null;
                $uniqueCode = Str::upper(Str::random(8));
                while (isset($existingUniqueCodes[$uniqueCode])) {
                    $uniqueCode = Str::upper(Str::random(8));
                }
                $existingUniqueCodes[$uniqueCode] = true;
                $upsertData[] = [
                    'student_id' => $nim,
                    'name' => trim($mhs['namaMhs'] ?? 'Mahasiswa'),
                    'study_program' => $programStudi,
                    'faculty' => $namaFakultas,
                    'phone_number' => $phoneNumber,
                    'unique_code' => $uniqueCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $countProcessed++;
                $bar->advance();
            }
            if (! empty($upsertData)) {
                Attendance::upsert(
                    $upsertData,
                    ['student_id'],
                    ['name', 'study_program', 'faculty', 'phone_number', 'updated_at']
                );
            }
        }
        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Berhasil memproses dan menyinkronkan '.$countProcessed.' data peserta tahun 2026.');

        return Command::SUCCESS;
    }
}
