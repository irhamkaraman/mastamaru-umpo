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
    protected $signature = 'umpo:sync-mahasiswa {--debug} {--rebalance : Ratakan jumlah anggota antar kelompok yang timpang tanpa mengacak ulang}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active students from UMPO API, translate majors, and manage group distributions.';

    /**
     * Filter sinkronisasi berdasarkan Jenis Kelas (reguler/transfer).
     * Anda dapat mengatur array ini untuk membatasi jenis kelas yang ditarik.
     *
     * @var array
     */
    protected $allowedJenis = ['Reguler', 'Reguler-Transfer'];

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
        $allDebugData = [];
        foreach ($chunks as $chunk) {
            $upsertData = [];
            foreach ($chunk as $mhs) {
                $nim = trim($mhs['nim'] ?? '');
                if (empty($nim)) {
                    $bar->advance();

                    continue;
                }
                
                $jenis = trim($mhs['jenis'] ?? '');
                if (!in_array($jenis, $this->allowedJenis)) {
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
                if ($this->option('debug')) {
                    $allDebugData = array_merge($allDebugData, $upsertData);
                } else {
                    Attendance::upsert(
                        $upsertData,
                        ['student_id'],
                        ['name', 'study_program', 'faculty', 'phone_number', 'updated_at']
                    );
                }
            }
        }
        $bar->finish();
        $this->newLine();
        
        if ($this->option('debug')) {
            $this->line(json_encode($allDebugData, JSON_PRETTY_PRINT));
            $this->info('DEBUG MODE: Menampilkan ' . count($allDebugData) . ' data siap simpan tanpa dimasukkan ke database.');
        } else {
            $this->info('Selesai! Berhasil memproses dan menyinkronkan '.$countProcessed.' data peserta tahun 2026.');
            
            $unassignedPeserta = Attendance::whereNull('group_id')
                ->orWhereNull('mentor_id')
                ->get();
            
            if ($unassignedPeserta->isNotEmpty()) {
                $this->info('Ditemukan ' . $unassignedPeserta->count() . ' peserta baru tanpa kelompok. Memasukkan ke kelompok terkecil...');
                $mentors = \App\Models\Mentor::withCount('attendances')->with('group')->get();
                if ($mentors->isNotEmpty()) {
                    foreach ($unassignedPeserta as $peserta) {
                        $targetMentor = $mentors->sortBy('attendances_count')->first();
                        $peserta->update([
                            'group_id' => $targetMentor->group_id,
                            'mentor_id' => $targetMentor->id,
                        ]);
                        $targetMentor->attendances_count++;
                    }
                    $this->info('✅ Berhasil memasukkan peserta baru ke kelompok secara proporsional.');
                }
            } else {
                $this->info('Semua peserta sudah memiliki kelompok. Tidak ada perubahan kelompok peserta lama.');
            }

            if ($this->option('rebalance')) {
                $this->newLine();
                $this->info('⚖️ Menjalankan REBALANCE KELOMPOK (Meratakan kelompok tanpa acak ulang)...');
                
                $mentors = \App\Models\Mentor::withCount('attendances')->with('group')->get();
                if ($mentors->isEmpty()) {
                    $this->warn('Belum ada data mentor untuk di-rebalance.');
                } else {
                    $totalAssigned = Attendance::whereNotNull('mentor_id')->count();
                    $mentorCount = $mentors->count();
                    $targetFloor = (int) floor($totalAssigned / $mentorCount);
                    $targetCeil = (int) ceil($totalAssigned / $mentorCount);

                    $this->line("    -> Total Peserta: {$totalAssigned} | Total Mentor: {$mentorCount}");
                    $this->line("    -> Target seimbang per kelompok: {$targetFloor} s/d {$targetCeil} peserta.");

                    $movedCount = 0;
                    
                    while (true) {
                        $mentors = \App\Models\Mentor::withCount('attendances')->with('group')->get();
                        $maxMentor = $mentors->sortByDesc('attendances_count')->first();
                        $minMentor = $mentors->sortBy('attendances_count')->first();

                        if (($maxMentor->attendances_count - $minMentor->attendances_count) <= 1) {
                            break;
                        }

                        $studentToMove = Attendance::where('mentor_id', $maxMentor->id)
                            ->orderBy('id', 'desc')
                            ->first();

                        if (! $studentToMove) {
                            break;
                        }

                        $studentToMove->update([
                            'group_id' => $minMentor->group_id,
                            'mentor_id' => $minMentor->id,
                        ]);

                        $movedCount++;
                    }

                    if ($movedCount > 0) {
                        $this->info("✅ Berhasil memindahkan {$movedCount} peserta paling akhir dari kelompok berlebih ke kelompok yang kekurangan sehingga rata.");
                    } else {
                        $this->info("✅ Semua kelompok sudah dalam kondisi rata dan seimbang. Tidak ada peserta yang perlu dipindahkan.");
                    }

                    $finalMentors = \App\Models\Mentor::withCount('attendances')->with('group')->get();
                    $summaryRows = $finalMentors->map(function ($m) {
                        return [
                            'Kelompok' => $m->group?->name ?? '-',
                            'Pendamping' => $m->name,
                            'Jumlah Peserta' => $m->attendances_count,
                        ];
                    })->toArray();
                    $this->table(['Kelompok', 'Pendamping', 'Jumlah Peserta'], $summaryRows);
                }
            }
        }

        return Command::SUCCESS;
    }
}
