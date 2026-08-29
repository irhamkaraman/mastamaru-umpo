<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="w-1/3">
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari NIM atau Nama..." class="w-full rounded-lg border-gray-300 dark:border-white/10 dark:bg-white/5 dark:text-white sm:text-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div>
            <!-- Pagination summary could go here -->
        </div>
    </div>

    @if(empty($paginator->items()))
        <div class="text-center py-8 text-gray-500 dark:text-gray-400 border border-dashed rounded-lg border-gray-300 dark:border-gray-700">
            Tidak ada data API yang ditemukan atau belum dipetakan.
        </div>
    @else
        <div class="overflow-x-auto ring-1 ring-gray-200 dark:ring-white/10 rounded-lg">
            <table class="w-full text-left divide-y divide-gray-200 dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white w-5/12">Data dari API (Baru)</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white w-5/12 border-l border-gray-200 dark:border-white/10">Data di Database Saat Ini</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white w-2/12 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @foreach($paginator->items() as $item)
                        @php
                            $nim = $item['nim'];
                            $dbData = $existingStudents[$nim] ?? null;
                            $isDbDuplicate = $item['is_db_duplicate'] ?? false;
                            $isListDuplicate = $item['is_list_duplicate'] ?? false;
                            $isDuplicate = $item['is_duplicate'] ?? false;
                            
                            $rowClass = "hover:bg-gray-50 dark:hover:bg-white/5 transition";
                            if ($isDuplicate) {
                                $rowClass = "bg-danger-50 hover:bg-danger-100 dark:bg-danger-500/10 dark:hover:bg-danger-500/20 transition";
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $item['nim'] }} - {{ $item['nama'] }}
                                    @if($isListDuplicate)
                                        <span class="ml-2 inline-flex items-center rounded-md bg-danger-100 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/20">
                                            Double di API
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $item['fakultas'] }} &bull; {{ $item['jurusan'] }}
                                    @if(!empty($item['telepon']) && $item['telepon'] !== '-')
                                        <div class="text-xs text-primary-600 dark:text-primary-400 font-medium mt-0.5">
                                            📞 {{ $item['telepon'] }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-4 py-3 align-top border-l border-gray-200 dark:border-white/10">
                                @if($dbData)
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $dbData['student_id'] }} - {{ $dbData['name'] }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $dbData['faculty'] ?? '-' }} &bull; {{ $dbData['study_program'] ?? '-' }}
                                        @if(!empty($dbData['phone_number']))
                                            <div class="text-xs text-success-600 dark:text-success-400 font-medium mt-0.5">
                                                📞 {{ $dbData['phone_number'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-800 ring-1 ring-inset ring-warning-600/20 mt-2">
                                        Duplikat (Sudah ada di DB)
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                        Data Baru (Belum di DB)
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-3 align-middle text-center space-y-2">
                                <button type="button" 
                                    wire:click="deleteItem('{{ $nim }}')"
                                    wire:confirm="Yakin ingin menghapus NIM {{ $nim }} dari daftar antrean API ini agar tidak ikut disinkronisasi?"
                                    class="w-full inline-flex justify-center items-center rounded bg-white px-2 py-1 text-xs font-semibold text-danger-600 shadow-sm ring-1 ring-inset ring-danger-300 hover:bg-gray-50 dark:bg-white/10 dark:text-danger-400 dark:ring-danger-500/30 dark:hover:bg-white/20">
                                    Hapus dari API
                                </button>
                                
                                @if($isDbDuplicate)
                                <button type="button" 
                                    wire:click="deleteFromDb('{{ $nim }}')"
                                    wire:confirm="PERINGATAN: Yakin ingin menghapus NIM {{ $nim }} secara permanen dari Database lokal (Tabel Peserta)?"
                                    class="w-full inline-flex justify-center items-center rounded bg-danger-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-danger-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-danger-600">
                                    Hapus dari Database
                                </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
