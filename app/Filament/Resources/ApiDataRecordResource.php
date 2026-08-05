<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiDataRecordResource\Pages;
use App\Filament\Resources\ApiDataRecordResource\RelationManagers;
use App\Models\ApiDataRecord;
use Filament\Tables\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiDataRecordResource extends Resource
{
    protected static ?string $model = ApiDataRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    
    protected static ?string $navigationGroup = 'Integrasi API';
    
    protected static ?string $navigationLabel = 'Data Hasil API';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Data')
                    ->description('Menampilkan dari konfigurasi API mana data ini berasal.')
                    ->disabled(fn (?ApiDataRecord $record) => $record && $record->is_imported)
                    ->schema([
                        Select::make('api_configuration_id')
                            ->label('Sumber API')
                            ->relationship('apiConfiguration', 'name')
                            ->helperText('Pilih konfigurasi API mana yang menghasilkan data ini. Satu sumber hanya bisa dipakai satu kali.')
                            ->unique(ignoreRecord: true)
                            ->live()
                            ->required(),
                        TextInput::make('external_id')
                            ->label('ID Eksternal (Dari API)')
                            ->helperText('Opsional. Simpan ID unik (Primary Key) dari sistem asal (pihak ketiga) untuk menghindari duplikasi data.'),
                    ])->columns(2),
                    
                Actions::make([
                    Action::make('fetch_data')
                        ->label('Tarik Data Sekarang (Fetch)')
                        ->icon('heroicon-m-cloud-arrow-down')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Hubungi Server API?')
                        ->modalDescription('Apakah Anda yakin ingin menarik data langsung dari server pihak ketiga sekarang?')
                        ->modalSubmitActionLabel('Ya, Tarik Data')
                        ->action(function (Get $get, Set $set) {
                            $configId = $get('api_configuration_id');
                            if (! $configId) {
                                Notification::make()->title('Gagal!')->body('Pilih Sumber API terlebih dahulu di atas!')->danger()->send();
                                return;
                            }
                            
                            $config = \App\Models\ApiConfiguration::find($configId);
                            if (! $config) return;

                            try {
                                $request = Http::timeout(30);
                                if (!empty($config->headers)) {
                                    $request->withHeaders($config->headers);
                                }
                                
                                $endpoint = $config->endpoint;
                                $method = strtolower($config->method ?? 'get');
                                
                                $payload = $config->body_payload ? json_decode($config->body_payload, true) : [];
                                
                                if ($method === 'get') {
                                    $response = $request->get($endpoint, $config->query_params ?? []);
                                } else {
                                    $url = $endpoint;
                                    if (!empty($config->query_params)) {
                                        $url .= '?' . http_build_query($config->query_params);
                                    }
                                    $response = $request->$method($url, $payload ?? []);
                                }
                                
                                if ($response->successful()) {
                                    $responseData = $response->json();
                                    $set('payload_data', json_encode($responseData, JSON_PRETTY_PRINT));
                                    
                                    // Auto-extract keys
                                    $dataArray = $responseData['data'] ?? $responseData;
                                    if (is_array($dataArray) && count($dataArray) > 0) {
                                        $firstItem = $dataArray[0];
                                        if (is_array($firstItem)) {
                                            $keys = array_keys($firstItem);
                                            $mapping = [];
                                            foreach ($keys as $key) {
                                                $guess = null;
                                                $keyLower = strtolower($key);
                                                if (str_contains($keyLower, 'nim') || str_contains($keyLower, 'student_id')) $guess = 'student_id';
                                                elseif (str_contains($keyLower, 'nama') || str_contains($keyLower, 'name')) $guess = 'name';
                                                elseif (str_contains($keyLower, 'fakultas') || str_contains($keyLower, 'faculty')) $guess = 'faculty';
                                                elseif (str_contains($keyLower, 'jurusan') || str_contains($keyLower, 'prodi') || str_contains($keyLower, 'program')) $guess = 'study_program';
                                                elseif (str_contains($keyLower, 'telepon') || str_contains($keyLower, 'hp') || str_contains($keyLower, 'phone')) $guess = 'phone_number';
                                                elseif (str_contains($keyLower, 'sex') || str_contains($keyLower, 'gender') || str_contains($keyLower, 'kelamin')) $guess = 'gender';
                                                
                                                $mapping[] = [
                                                    'api_key' => $key,
                                                    'db_column' => $guess,
                                                ];
                                            }
                                            $set('response_mapping', $mapping);
                                        }
                                    }
                                    
                                    Notification::make()->title('Sukses!')->body('Data berhasil ditarik. Silakan atur pemetaan (mapping) di bawah ini lalu Simpan.')->success()->send();
                                } else {
                                    Notification::make()->title('Gagal: HTTP ' . $response->status())->body($response->body())->danger()->send();
                                }
                            } catch (\Exception $e) {
                                Notification::make()->title('Error Koneksi API')->body($e->getMessage())->danger()->send();
                            }
                        })
                        ->hidden(fn (?ApiDataRecord $record) => $record && $record->is_imported),
                        
                    Action::make('sync_data')
                        ->label('Sinkronisasi ke Data Peserta')
                        ->icon('heroicon-m-users')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Sinkronisasi Data?')
                        ->modalDescription('Apakah Anda yakin ingin memasukkan data API ini ke tabel Peserta (Attendance)? Tindakan ini akan mengunci data agar tidak bisa ditarik ulang.')
                        ->modalSubmitActionLabel('Ya, Sinkronisasi')
                        ->hidden(fn (?ApiDataRecord $record) => ! $record || $record->is_imported)
                        ->action(function (?ApiDataRecord $record, Get $get) {
                            if (!$record) {
                                Notification::make()->title('Simpan Dulu!')->body('Harap klik Save Changes terlebih dahulu.')->warning()->send();
                                return;
                            }
                            
                            $payloadStr = $get('payload_data');
                            if (empty($payloadStr)) return;
                            
                            $payload = json_decode($payloadStr, true);
                            $data = $payload['data'] ?? $payload;
                            
                            if (!is_array($data)) {
                                Notification::make()->title('Gagal: Format data tidak valid (bukan array).')->danger()->send();
                                return;
                            }
                            
                            $mappingRules = $record->response_mapping ?? [];
                            if (empty($mappingRules)) {
                                Notification::make()->title('Gagal: Pemetaan kosong!')->body('Tabel pemetaan di bawah belum diisi atau disimpan.')->danger()->send();
                                return;
                            }
                            
                            $count = 0;
                            foreach ($data as $item) {
                                // Find which JSON key maps to student_id (NIM) as it's required
                                $studentIdKey = null;
                                foreach ($mappingRules as $rule) {
                                    if ($rule['db_column'] === 'student_id') {
                                        $studentIdKey = $rule['api_key'];
                                        break;
                                    }
                                }
                                
                                if (!$studentIdKey || !isset($item[$studentIdKey])) continue;
                                
                                $mappedData = [];
                                foreach ($mappingRules as $rule) {
                                    $apiKey = $rule['api_key'];
                                    $dbColumn = $rule['db_column'];
                                    
                                    if (isset($item[$apiKey])) {
                                        if ($dbColumn === 'gender') {
                                            $val = $item[$apiKey];
                                            $mappedData[$dbColumn] = $val === 'L' ? 'Laki-laki' : ($val === 'P' ? 'Perempuan' : $val);
                                        } else {
                                            $mappedData[$dbColumn] = $item[$apiKey];
                                        }
                                    }
                                }
                                
                                \App\Models\Attendance::updateOrCreate(
                                    ['student_id' => $item[$studentIdKey]],
                                    $mappedData
                                );
                                $count++;
                            }
                            
                            $record->update(['is_imported' => true]);
                            
                            Notification::make()->title('Berhasil!')->body("$count peserta berhasil disinkronisasi.")->success()->send();
                        })
                ])->columnSpanFull(),
                    
                Forms\Components\Section::make('Pemetaan Kolom & Data Mentah')
                    ->disabled(fn (?ApiDataRecord $record) => $record && $record->is_imported)
                    ->schema([
                        Forms\Components\Repeater::make('response_mapping')
                            ->label('Tabel Pemetaan Kolom (Mapping)')
                            ->helperText('Cocokkan kolom dari API (Kiri) ke kolom Peserta di sistem (Kanan). Biarkan kosong jika tidak dipakai.')
                            ->schema([
                                TextInput::make('api_key')
                                    ->label('Atribut dari API')
                                    ->required()
                                    ->readOnly(),
                                Select::make('db_column')
                                    ->label('Pasangkan ke Kolom Peserta')
                                    ->options([
                                        'student_id' => 'NIM (student_id) - Wajib',
                                        'name' => 'Nama Peserta (name) - Wajib',
                                        'faculty' => 'Fakultas (faculty)',
                                        'study_program' => 'Program Studi (study_program)',
                                        'phone_number' => 'No Telepon (phone_number)',
                                        'gender' => 'Jenis Kelamin (gender)',
                                    ]),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->reorderable(false)
                            ->deletable(false)
                            ->addable(false),
                            
                        Textarea::make('payload_data')
                            ->label('Data Lengkap (Format JSON)')
                            ->helperText('Seluruh nilai JSON yang dikembalikan oleh API (nama, fakultas, dll) tersimpan utuh di dalam kolom ini.')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('apiConfiguration.name')->sortable()->searchable(),
                TextColumn::make('external_id')->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiDataRecords::route('/'),
            'create' => Pages\CreateApiDataRecord::route('/create'),
            'edit' => Pages\EditApiDataRecord::route('/{record}/edit'),
        ];
    }
}
