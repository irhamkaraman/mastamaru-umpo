<div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
    <div class="fi-section-header px-6 py-4">
        <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
            Perbandingan Data API vs Database
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Gunakan tabel di bawah untuk melihat perbedaan, menghapus data yang tidak diperlukan, atau mensinkronkan data API dengan database lokal.
        </p>
    </div>
    <div class="fi-section-content p-6 border-t border-gray-200 dark:border-white/10">
        @if($getRecord())
            @livewire(\App\Livewire\ApiDataComparisonGrid::class, ['recordId' => $getRecord()->id])
        @else
            <p class="text-gray-500 text-center py-4">Simpan konfigurasi terlebih dahulu untuk melihat perbandingan data.</p>
        @endif
    </div>
</div>
