<?php

namespace App\Livewire;

use App\Models\ApiDataRecord;
use App\Models\Attendance;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Filament\Notifications\Notification;

class ApiDataComparisonGrid extends Component
{
    use WithPagination;

    public $recordId;
    public $search = '';
    
    // Using simple theme for pagination
    protected $paginationTheme = 'tailwind';

    public function mount($recordId)
    {
        $this->recordId = $recordId;
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getRecordProperty()
    {
        return ApiDataRecord::find($this->recordId);
    }
    
    public function getMappingRulesProperty()
    {
        return $this->record->response_mapping ?? [];
    }

    public function deleteItem($apiStudentId)
    {
        $record = $this->record;
        $payloadStr = $record->payload_data;
        if (empty($payloadStr)) return;
        
        $payload = json_decode($payloadStr, true);
        $data = $payload['data'] ?? $payload;
        
        if (!is_array($data)) return;
        
        $studentIdKey = $this->getStudentIdKey();
        if (!$studentIdKey) return;
        
        // Filter out the item
        $newData = array_filter($data, function($item) use ($studentIdKey, $apiStudentId) {
            return isset($item[$studentIdKey]) && $item[$studentIdKey] !== $apiStudentId;
        });
        
        // Re-index array
        $newData = array_values($newData);
        
        if (isset($payload['data'])) {
            $payload['data'] = $newData;
        } else {
            $payload = $newData;
        }
        
        $record->update([
            'payload_data' => json_encode($payload, JSON_PRETTY_PRINT)
        ]);
        
        Notification::make()->title('Berhasil')->body('Data mahasiswa dengan NIM ' . $apiStudentId . ' dihapus dari antrean sinkronisasi.')->success()->send();
    }
    
    public function deleteFromDb($studentId)
    {
        $deleted = Attendance::where('student_id', $studentId)->delete();
        if ($deleted) {
            Notification::make()->title('Berhasil')->body('Data mahasiswa dengan NIM ' . $studentId . ' telah dihapus dari Database.')->success()->send();
        }
    }

    private function getStudentIdKey()
    {
        foreach ($this->mappingRules as $rule) {
            if ($rule['db_column'] === 'student_id') {
                return $rule['api_key'];
            }
        }
        return null;
    }

    public function render()
    {
        $record = $this->record;
        $items = [];
        $existingStudents = [];
        
        if ($record && !empty($record->payload_data)) {
            $payload = json_decode($record->payload_data, true);
            $data = $payload['data'] ?? $payload;
            
            if (is_array($data)) {
                $studentIdKey = $this->getStudentIdKey();
                
                $nameKey = null;
                $jurusanKey = null;
                $fakultasKey = null;
                $phoneKey = null;
                
                foreach ($this->mappingRules as $rule) {
                    if ($rule['db_column'] === 'name') $nameKey = $rule['api_key'];
                    if ($rule['db_column'] === 'study_program') $jurusanKey = $rule['api_key'];
                    if ($rule['db_column'] === 'faculty') $fakultasKey = $rule['api_key'];
                    if ($rule['db_column'] === 'phone_number') $phoneKey = $rule['api_key'];
                }
                
                // Get all NIMs to pre-fetch existing students
                $allNims = [];
                foreach ($data as $item) {
                    if ($studentIdKey && isset($item[$studentIdKey])) {
                        $allNims[] = $item[$studentIdKey];
                    }
                }
                
                // Fetch all existing students that match the API payload
                $allNims = array_unique(array_filter($allNims, fn($n) => $n !== '-'));
                if (count($allNims) > 0) {
                    foreach (array_chunk($allNims, 1000) as $chunk) {
                        $existing = Attendance::whereIn('student_id', $chunk)->get()->keyBy('student_id')->toArray();
                        $existingStudents = array_merge($existingStudents, $existing);
                    }
                }

                $duplicates = [];
                $newItems = [];
                $seenNims = [];

                foreach ($data as $item) {
                    $nim = $studentIdKey && isset($item[$studentIdKey]) ? $item[$studentIdKey] : '-';
                    $nama = $nameKey && isset($item[$nameKey]) ? $item[$nameKey] : '-';
                    $telepon = $phoneKey && isset($item[$phoneKey]) ? $item[$phoneKey] : ($item['teleponMhs'] ?? $item['telepon'] ?? '-');
                    
                    if (!empty($this->search)) {
                        if (stripos($nim, $this->search) === false && stripos($nama, $this->search) === false && stripos($telepon, $this->search) === false) {
                            continue;
                        }
                    }
                    
                    $isDbDuplicate = isset($existingStudents[$nim]);
                    $isListDuplicate = isset($seenNims[$nim]);
                    
                    $row = [
                        'nim' => $nim,
                        'nama' => $nama,
                        'telepon' => $telepon,
                        'jurusan' => $jurusanKey && isset($item[$jurusanKey]) ? $item[$jurusanKey] : '-',
                        'fakultas' => $fakultasKey && isset($item[$fakultasKey]) ? $item[$fakultasKey] : '-',
                        'is_db_duplicate' => $isDbDuplicate,
                        'is_list_duplicate' => $isListDuplicate,
                        'is_duplicate' => $isDbDuplicate || $isListDuplicate,
                    ];
                    
                    if ($row['is_duplicate']) {
                        $duplicates[] = $row;
                    } else {
                        $newItems[] = $row;
                    }
                    
                    $seenNims[$nim] = true;
                }
                
                // Smart Algorithm: Put duplicates at the top
                $items = array_merge($duplicates, $newItems);
            }
        }
        
        // Manual pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = array_slice($items, ($currentPage - 1) * $perPage, $perPage);
        
        $paginator = new LengthAwarePaginator(
            $currentItems,
            count($items),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.api-data-comparison-grid', [
            'paginator' => $paginator,
            'existingStudents' => $existingStudents,
        ]);
    }
}
