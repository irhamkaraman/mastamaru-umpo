<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiDataRecordResource\Pages;
use App\Models\ApiDataRecord;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Log;

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
                Forms\Components\Placeholder::make('tutorial_sync')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString('
                        <div class="p-4 rounded-lg bg-warning-50 border border-warning-200 dark:bg-warning-900/30 dark:border-warning-800 text-warning-800 dark:text-warning-300 text-sm mb-2 shadow-sm">
                            <strong class="block mb-2 text-base flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Langkah Memasukkan Data ke Tabel Peserta (Insert)</strong>
                            <ol class="list-decimal ml-5 space-y-1">
                                <li>Klik tombol hijau <strong>Tarik Data Sekarang (Fetch)</strong> untuk memuat data mentah ke tabel sementara di bawah.</li>
                                <li>Pilih dan sesuaikan pasangan antar atribut (Mapping) di tabel <strong>Pemetaan Kolom</strong> (contoh: NIM disilang dengan nim).</li>
                                <li>Klik tombol <strong class="text-primary-600 dark:text-primary-400">Save Changes</strong> (di pojok kanan atas/bawah) untuk menyimpan aturan pemetaan (Wajib!).</li>
                                <li>Terakhir, klik tombol kuning <strong>Sinkronisasi ke Data Peserta</strong> untuk memasukkan data-data tersebut secara nyata ke Database.</li>
                            </ol>
                        </div>
                    '))
                    ->columnSpanFull()
                    ->hidden(fn (?ApiDataRecord $record) => $record && $record->is_imported),
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
                            if (! $config) {
                                return;
                            }
                            try {
                                $request = Http::timeout(30);
                                $headers = $config->headers ?? [];
                                $endpoint = $config->endpoint;
                                if (str_contains($endpoint, '76.76.76.185') || str_contains($endpoint, 'api.umpo.ac.id') || str_contains($endpoint, 'apikey.umpo.ac.id')) {
                                    $accesscode = 'd6e2ec2be6d9527a21f034e1bee325b5ce4d2154cb0475943f1880c3fcbcee11';
                                    try {
                                        $tokenUrl = 'https://apikey.umpo.ac.id/generate-token?'.http_build_query([
                                            'apiLink' => 'http://76.76.76.185:8088/api-key/mahasiswas/find-all',
                                            'accesscodeTalker' => $accesscode,
                                        ]);
                                        $tokenResponse = Http::timeout(10)
                                            ->withoutVerifying()
                                            ->withHeaders(['Accept' => 'application/json'])
                                            ->post($tokenUrl);
                                        if ($tokenResponse->successful()) {
                                            $tokenData = $tokenResponse->json();
                                            $finalToken = $tokenData['token'] ?? null;
                                            if ($finalToken) {
                                                $authReplaced = false;
                                                foreach ($headers as $k => $v) {
                                                    if (strtolower($k) === 'authorization') {
                                                        $headers[$k] = $finalToken;
                                                        $authReplaced = true;
                                                    }
                                                }
                                                if (! $authReplaced) {
                                                    $headers['Authorization'] = $finalToken;
                                                }
                                            }
                                        } else {
                                            Log::error('UMPO Token API Failed', ['status' => $tokenResponse->status(), 'body' => $tokenResponse->body()]);
                                        }
                                    } catch (Exception $e) {
                                        Log::error('UMPO Token API Exception', ['error' => $e->getMessage()]);
                                    }
                                }
                                if (! empty($headers)) {
                                    $request = $request->withHeaders($headers);
                                }
                                $endpoint = $config->endpoint;
                                $method = strtolower($config->method ?? 'get');
                                $payload = $config->body_payload ? json_decode($config->body_payload, true) : [];
                                if (str_contains($endpoint, 'umpo.ac.id') || str_contains($endpoint, '76.76.76.185')) {
                                    $request = $request->withoutVerifying();
                                }
                                if ($method === 'get') {
                                    $response = $request->get($endpoint, $config->query_params ?? []);
                                } else {
                                    $url = $endpoint;
                                    if (! empty($config->query_params)) {
                                        $url .= '?'.http_build_query($config->query_params);
                                    }
                                    $response = $request->$method($url, $payload ?? []);
                                }
                                if ($response->successful()) {
                                    $responseData = $response->json();
                                    $set('payload_data', json_encode($responseData, JSON_PRETTY_PRINT));
                                    $dataArray = $responseData['data'] ?? $responseData;
                                    if (is_array($dataArray) && count($dataArray) > 0) {
                                        $firstItem = $dataArray[0];
                                        if (is_array($firstItem)) {
                                            $keys = array_keys($firstItem);
                                            $dbColumns = [
                                                'student_id', 'name', 'faculty', 'study_program', 'phone_number', 'gender',
                                            ];
                                            $mapping = [];
                                            foreach ($dbColumns as $dbCol) {
                                                $guess = null;
                                                foreach ($keys as $key) {
                                                    $keyLower = strtolower($key);
                                                    if ($dbCol === 'student_id' && (str_contains($keyLower, 'nim') || str_contains($keyLower, 'student_id'))) {
                                                        $guess = $key;
                                                    } elseif ($dbCol === 'name' && (str_contains($keyLower, 'nama') || str_contains($keyLower, 'name'))) {
                                                        $guess = $key;
                                                    } elseif ($dbCol === 'faculty' && (str_contains($keyLower, 'fakultas') || str_contains($keyLower, 'faculty'))) {
                                                        $guess = $key;
                                                    } elseif ($dbCol === 'study_program' && (str_contains($keyLower, 'jurusan') || str_contains($keyLower, 'prodi') || str_contains($keyLower, 'program'))) {
                                                        $guess = $key;
                                                    } elseif ($dbCol === 'phone_number' && (str_contains($keyLower, 'telepon') || str_contains($keyLower, 'telp') || str_contains($keyLower, 'hp') || str_contains($keyLower, 'phone') || str_contains($keyLower, 'wa'))) {
                                                        $guess = $key;
                                                    } elseif ($dbCol === 'gender' && (str_contains($keyLower, 'sex') || str_contains($keyLower, 'gender') || str_contains($keyLower, 'kelamin'))) {
                                                        $guess = $key;
                                                    }
                                                    if ($guess) {
                                                        break;
                                                    }
                                                }
                                                $mapping[] = [
                                                    'db_column' => $dbCol,
                                                    'api_key' => $guess,
                                                ];
                                            }
                                            $set('response_mapping', $mapping);
                                        }
                                    }
                                    Notification::make()->title('Sukses!')->body('Data berhasil ditarik. Silakan atur pemetaan (mapping) di bawah ini lalu Simpan.')->success()->send();
                                } else {
                                    Notification::make()->title('Gagal: HTTP '.$response->status())->body($response->body())->danger()->send();
                                }
                            } catch (Exception $e) {
                                Notification::make()->title('Error Koneksi API')->body($e->getMessage())->danger()->send();
                            }
                        })
                        ->visible(true),
                    Action::make('sync_data')
                        ->label('Sinkronisasi ke Data Peserta')
                        ->icon('heroicon-m-users')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Sinkronisasi Data?')
                        ->modalDescription('Apakah Anda yakin ingin memasukkan data API ini ke tabel Peserta (Attendance)? Tindakan ini akan mengunci data agar tidak bisa ditarik ulang.')
                        ->modalSubmitActionLabel('Ya, Sinkronisasi')
                        ->visible(true)
                        ->action(function (?ApiDataRecord $record, Get $get) {
                            if (! $record) {
                                Notification::make()->title('Simpan Dulu!')->body('Harap klik Save Changes terlebih dahulu.')->warning()->send();

                                return;
                            }
                            $payloadStr = $get('payload_data');
                            if (empty($payloadStr)) {
                                return;
                            }
                            $payload = json_decode($payloadStr, true);
                            $data = $payload['data'] ?? $payload;
                            if (! is_array($data)) {
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
                                $studentIdKey = null;
                                foreach ($mappingRules as $rule) {
                                    if ($rule['db_column'] === 'student_id') {
                                        $studentIdKey = $rule['api_key'];
                                        break;
                                    }
                                }
                                if (! $studentIdKey || ! isset($item[$studentIdKey])) {
                                    continue;
                                }
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
                        }),
                ])->columnSpanFull(),
                Forms\Components\Section::make('Pemetaan Kolom & Data Mentah')
                    ->schema([
                        Forms\Components\Repeater::make('response_mapping')
                            ->label('Tabel Pemetaan Kolom (Mapping)')
                            ->helperText('Pilih atribut API mana yang sesuai dengan kolom Peserta di sistem kita. Biarkan kosong jika tidak dipakai.')
                            ->schema([
                                Select::make('db_column')
                                    ->label('Kolom Peserta (Target)')
                                    ->options([
                                        'student_id' => 'NIM (student_id) - Wajib',
                                        'name' => 'Nama Peserta (name) - Wajib',
                                        'faculty' => 'Fakultas (faculty)',
                                        'study_program' => 'Program Studi (study_program)',
                                        'phone_number' => 'No Telepon (phone_number)',
                                        'gender' => 'Jenis Kelamin (gender)',
                                    ])
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(),
                                Select::make('api_key')
                                    ->label('Pilih Atribut dari API')
                                    ->options(function (Get $get) {
                                        $payload = $get('../../payload_data');
                                        if (! $payload) {
                                            return [];
                                        }
                                        $data = json_decode($payload, true);
                                        $dataArray = $data['data'] ?? $data;
                                        if (is_array($dataArray) && count($dataArray) > 0) {
                                            $keys = array_keys(is_array($dataArray[0]) ? $dataArray[0] : []);

                                            return array_combine($keys, $keys);
                                        }

                                        return [];
                                    })
                                    ->searchable(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->reorderable(false)
                            ->deletable(false)
                            ->addable(false),
                        Forms\Components\ViewField::make('payload_data')
                            ->label('Data Lengkap')
                            ->view('filament.components.api-data-comparison')
                            ->columnSpanFull(),
                    ]),
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
