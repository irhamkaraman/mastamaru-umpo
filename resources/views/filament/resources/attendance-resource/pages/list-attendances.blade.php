<x-filament-panels::page>
    <div class="p-4 rounded-lg bg-warning-50 border border-warning-200 dark:bg-warning-900/30 dark:border-warning-800 text-warning-800 dark:text-warning-300 text-sm shadow-sm mb-2">
        <strong class="block mb-2 text-base flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 
            Informasi Tombol Tarik Mahasiswa Aktif
        </strong>
        <p class="mb-1">
            Tombol hijau <strong>Tarik Mahasiswa Aktif UMPO</strong> di atas <u>BUKAN</u> berfungsi untuk memasukkan data baru ke tabel ini. Tombol tersebut berfungsi secara spesifik untuk <strong>menerjemahkan dan memperbarui (update)</strong> Kode Fakultas & Jurusan yang masih berupa angka (dari API) menjadi nama aslinya.
        </p>
        <p>
            Jika tabel di bawah ini masih kosong dan Anda ingin menarik data baru dari API UMPO, silakan kunjungi halaman <a href="/admin/api-data-records" class="font-bold underline text-warning-900 dark:text-warning-100">Data Hasil API</a> lalu lakukan langkah <strong>Sinkronisasi</strong> di sana.
        </p>
    </div>

    {{ $this->table }}
</x-filament-panels::page>