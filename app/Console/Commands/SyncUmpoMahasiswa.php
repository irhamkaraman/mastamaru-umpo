<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Illuminate\Console\Command;

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
            $jurusanResponse = \Illuminate\Support\Facades\Http::timeout(30)->get($jurusanUrl);
            
            if (!$jurusanResponse->successful()) {
                $this->error('Gagal mengambil data Jurusan: HTTP ' . $jurusanResponse->status());
                return Command::FAILURE;
            }

            $jurusanData = $jurusanResponse->json('data') ?? [];
        } catch (\Exception $e) {
            $this->error('Error koneksi API Jurusan: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $jurusanDict = [];
        $fakultasDict = [];
        foreach ($jurusanData as $j) {
            $kodeFak = $j['kodeFakultas'] ?? '';
            $kodeJur = $j['kodeJurusan'] ?? '';
            $key = $kodeFak . '-' . $kodeJur;
            $jurusanDict[$key] = $j['programStudi'] ?? $j['namaJurusan'] ?? '';
            $fakultasDict[$key] = $j['kelas'] ?? $kodeFak;
        }
        
        $this->info('Berhasil membuat kamus untuk ' . count($jurusanDict) . ' jurusan.');
        $this->info('Fetching Data Mahasiswa Aktif dari API UMPO...');
        
        $mhsUrl = 'https://apikey.umpo.ac.id/api/mahasiswa/find-all-mhs-aktifs';
        try {
            $mhsResponse = \Illuminate\Support\Facades\Http::timeout(60)->get($mhsUrl);
            
            if (!$mhsResponse->successful()) {
                $this->error('Gagal mengambil data Mahasiswa: HTTP ' . $mhsResponse->status());
                return Command::FAILURE;
            }
            
            $mhsData = $mhsResponse->json('data') ?? [];
        } catch (\Exception $e) {
            $this->error('Error koneksi API Mahasiswa: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        $existingStudents = Attendance::pluck('id', 'student_id')->toArray();
        $this->info('Ditemukan ' . count($mhsData) . ' mahasiswa aktif dari API. Mencocokkan dengan ' . count($existingStudents) . ' peserta di database...');
        
        $bar = $this->output->createProgressBar(count($mhsData));
        $bar->start();
        
        $countUpdated = 0;
        foreach ($mhsData as $mhs) {
            $nim = $mhs['nim'] ?? null;
            
            if (empty($nim) || !isset($existingStudents[$nim])) {
                $bar->advance();
                continue;
            }
            
            $kodeFak = $mhs['kodeFakultas'] ?? '';
            $kodeJur = $mhs['kodeJurusan'] ?? '';
            $dictKey = $kodeFak . '-' . $kodeJur;
            
            $programStudi = $jurusanDict[$dictKey] ?? $kodeJur;
            $namaFakultas = $fakultasDict[$dictKey] ?? $kodeFak;
            
            Attendance::where('student_id', $nim)->update([
                'name' => $mhs['namaMhs'],
                'study_program' => $programStudi,
                'faculty' => $namaFakultas,
            ]);
            
            $countUpdated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Berhasil mencocokkan dan memperbarui ' . $countUpdated . ' data peserta.');
        return Command::SUCCESS;
    }
}
