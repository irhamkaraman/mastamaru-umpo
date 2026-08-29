<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Mentor;
use App\Models\CertificateTemplate;
use App\Imports\AttendanceImport;
use App\Exports\AttendanceTemplateExport;
use App\Exports\AttendanceDataExport;
use App\Services\WordCertificateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Peserta';

    protected static ?string $modelLabel = 'Peserta';

    protected static ?string $pluralModelLabel = 'Peserta';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'name')
                    ->required()
                    ->label('Kelompok')
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('mentor_id', null))
                    ->options(function () {
                        $groups = Group::all();
                        if ($groups->isEmpty()) {
                            throw new \Exception('Tidak ada kelompok yang tersedia. Silakan buat kelompok terlebih dahulu.');
                        }
                        return $groups->pluck('name', 'id');
                    }),
                Forms\Components\Select::make('mentor_id')
                    ->relationship('mentor', 'name')
                    ->required()
                    ->label('Pendamping')
                    ->searchable()
                    ->reactive()
                    ->options(function (callable $get) {
                        $groupId = $get('group_id');
                        if ($groupId) {
                            $mentors = Mentor::where('group_id', $groupId)->get();
                        } else {
                            $mentors = Mentor::all();
                        }

                        if ($mentors->isEmpty()) {
                            return [];
                        }
                        return $mentors->pluck('name', 'id');
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Peserta'),
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('NIM'),
                Forms\Components\Select::make('faculty')
                    ->label('Fakultas')
                    ->placeholder('Pilih fakultas')
                    ->searchable()
                    ->options([
                        'Fakultas Teknik' => 'Fakultas Teknik',
                        'Fakultas Ekonomi' => 'Fakultas Ekonomi',
                        'Fakultas Ilmu Sosial dan Politik' => 'Fakultas Ilmu Sosial dan Politik',
                        'Fakultas Hukum' => 'Fakultas Hukum',
                        'Fakultas Ilmu Kesehatan' => 'Fakultas Ilmu Kesehatan',
                        'Fakultas Keguruan dan Ilmu Pendidikan' => 'Fakultas Keguruan dan Ilmu Pendidikan',
                        'Fakultas Agama Islam' => 'Fakultas Agama Islam',
                    ])
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Fakultas')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama fakultas baru')
                    ])
                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                        return $action
                            ->modalHeading('Tambah Fakultas Baru')
                            ->modalSubmitActionLabel('Tambah')
                            ->modalWidth('md');
                    })
                    ->createOptionUsing(function (array $data): string {
                        return $data['name'];
                    })
                    ->required(),
                Forms\Components\TextInput::make('study_program')
                    ->label('Program Studi')
                    ->placeholder('Masukkan program studi')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Nomor WhatsApp / Telepon')
                    ->placeholder('Contoh: 081234567890')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unique_code')
                    ->maxLength(255)
                    ->label('Kode Unik')
                    ->disabled()
                    ->dehydrated(false)
                    ->hiddenOn('create')
                    ->helperText('Kode unik akan dibuat otomatis'),
                Forms\Components\ViewField::make('barcode_image')
                    ->label('Barcode')
                    ->view('filament.forms.components.barcode-display')
                    ->dehydrated(false)
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Peserta'),
                Tables\Columns\TextColumn::make('student_id')
                    ->searchable()
                    ->sortable()
                    ->label('NIM'),
                Tables\Columns\TextColumn::make('faculty')
                    ->searchable()
                    ->sortable()
                    ->label('Fakultas')
                    ->placeholder('Tidak ada fakultas'),
                Tables\Columns\TextColumn::make('study_program')
                    ->searchable()
                    ->sortable()
                    ->label('Program Studi')
                    ->placeholder('Tidak ada program studi'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->label('No. WA / Telp')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('group.name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Kelompok'),
                Tables\Columns\TextColumn::make('mentor.name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Pendamping'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lulus' => 'success',
                        'gagal' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'lulus' => 'LULUS',
                        'gagal' => 'GAGAL',
                        default => 'PROSES',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->searchable()
                    ->sortable()
                    ->label('Kode Unik')
                    ->placeholder('Tidak ada kode unik'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat Pada'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui Pada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_id')
                    ->options(function () {
                        return Group::distinct()->pluck('name', 'id')->toArray();
                    })
                    ->label('Kelompok')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('mentor_id')
                    ->options(function () {
                        return Mentor::distinct()->pluck('name', 'id')->toArray();
                    })
                    ->label('Pendamping')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('faculty')
                    ->options(function () {
                        return Attendance::whereNotNull('faculty')
                            ->where('faculty', '!=', '')
                            ->distinct()
                            ->pluck('faculty', 'faculty')
                            ->toArray();
                    })
                    ->label('Fakultas')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('study_program')
                    ->label('Program Studi')
                    ->searchable()
                    ->options(fn () => Attendance::distinct()->pluck('study_program', 'study_program')->toArray()),
            ])
            ->headerActions([
                Tables\Actions\Action::make('sync_umpo')
                    ->label('Tarik Mahasiswa Aktif UMPO')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color('success')
                    ->modalHeading('Sinkronisasi Mahasiswa Aktif dari API UMPO (Tahun 2026)')
                    ->modalWidth('3xl')
                    ->modalFooterActions([])
                    ->modalContent(fn () => view('filament.modals.umpo-sync-progress')),
                Tables\Actions\Action::make('randomize_groups')
                    ->label('Bagi Kelompok Acak')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Bagi Peserta Secara Acak?')
                    ->modalDescription('Fitur ini akan membagikan semua peserta yang BELUM memiliki kelompok secara merata dan acak ke seluruh Pendamping (Mentor) yang tersedia. Lanjutkan?')
                    ->action(function () {
                        $unassignedPeserta = Attendance::whereNull('group_id')
                            ->orWhereNull('mentor_id')
                            ->inRandomOrder()
                            ->get();
                        
                        if ($unassignedPeserta->isEmpty()) {
                            Notification::make()->title('Info')->body('Semua peserta saat ini sudah memiliki kelompok & pendamping!')->warning()->send();
                            return;
                        }

                        $mentors = Mentor::with('group')->get();
                        
                        if ($mentors->isEmpty()) {
                            Notification::make()->title('Gagal')->body('Belum ada data Pendamping! Buat pendamping terlebih dahulu.')->danger()->send();
                            return;
                        }

                        $mentorCount = $mentors->count();
                        $index = 0;

                        foreach ($unassignedPeserta as $peserta) {
                            $mentor = $mentors[$index % $mentorCount];
                            
                            $peserta->update([
                                'group_id' => $mentor->group_id,
                                'mentor_id' => $mentor->id,
                            ]);
                            
                            $index++;
                        }
                        
                        Notification::make()->title('Sukses!')->body('Berhasil membagikan ' . $unassignedPeserta->count() . ' peserta ke kelompok secara merata dan acak.')->success()->send();
                    })
                    ->tooltip('Bagikan peserta yang belum punya kelompok secara otomatis'),
                    
                Tables\Actions\Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->visible(fn () => (auth()->user()?->can('export', Attendance::class) ?? false) && Group::exists() && Mentor::exists()) /** @phpstan-ignore-line */
                    ->action(function ($livewire) {
                        try {
                            ini_set('memory_limit', '2048M');
                            ini_set('max_execution_time', 600);

                            $filters = $livewire->tableFilters ?? [];
                            return Excel::download(new AttendanceDataExport($filters), 'data-peserta-' . date('Y-m-d-H-i-s') . '.csv', \Maatwebsite\Excel\Excel::CSV);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Gagal')
                                ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    })
                    ->tooltip('Export data peserta (sesuai filter aktif) ke file CSV'),
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn () => (auth()->user()?->can('export', Attendance::class) ?? false) && Group::exists() && Mentor::exists()) /** @phpstan-ignore-line */
                    ->action(function ($livewire) {
                        try {
                            ini_set('memory_limit', '2048M');
                            ini_set('max_execution_time', 600);

                            $filters = $livewire->tableFilters ?? [];
                            $filename = 'data-peserta-' . date('Y-m-d-H-i-s') . '.xlsx';

                            return Excel::download(new AttendanceDataExport($filters), $filename);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Gagal')
                                ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    })
                    ->tooltip('Export data peserta (sesuai filter aktif) ke format Excel (.xlsx)'),
                Tables\Actions\Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn () => (auth()->user()?->can('downloadTemplate', Attendance::class) ?? false) && Group::exists() && Mentor::exists()) /** @phpstan-ignore-line */
                    ->action(function () {
                        return Excel::download(new AttendanceTemplateExport, 'template-peserta.xlsx');
                    }),
                Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->visible(fn () => (auth()->user()?->can('import', Attendance::class) ?? false) && Group::exists() && Mentor::exists()) /** @phpstan-ignore-line */
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->disk('public')
                            ->directory('imports')
                            ->helperText('Upload file Excel dengan format .xlsx atau .xls.')
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = storage_path('app/public/' . $data['file']);
                            $import = new AttendanceImport();

                            Excel::import($import, $filePath);

                            $importedCount = $import->getImportedCount();
                            $skippedCount = $import->getSkippedCount();
                            $failures = $import->failures();
                            $errors = $import->errors();

                            if ($importedCount > 0) {
                                if ($skippedCount > 0) {
                                    Notification::make()
                                        ->title('Import Berhasil dengan Peringatan')
                                        ->body("Berhasil mengimpor {$importedCount} peserta. {$skippedCount} data dilewati karena duplikat atau tidak valid.")
                                        ->warning()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Import Berhasil')
                                        ->body("Berhasil mengimpor {$importedCount} peserta.")
                                        ->success()
                                        ->send();
                                }
                            } elseif ($skippedCount > 0) {
                                Notification::make()
                                    ->title('Import Gagal')
                                    ->body("Semua {$skippedCount} data dilewati karena duplikat atau tidak valid.")
                                    ->danger()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Import Gagal')
                                    ->body('Tidak ada data yang berhasil diimpor.')
                                    ->danger()
                                    ->send();
                            }

                            if (!empty($failures) || !empty($errors)) {
                                $errorMessages = [];
                                foreach ($failures as $failure) {
                                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                                }
                                foreach ($errors as $error) {
                                    $errorMessages[] = $error;
                                }

                                Notification::make()
                                    ->title('Detail Error')
                                    ->body(implode('\n', array_slice($errorMessages, 0, 5)))
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalWidth('md')
            ])
            ->actions([
                Tables\Actions\Action::make('point_history')
                    ->label('Riwayat Poin')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->url(fn (Attendance $record): string => static::getUrl('point_history', ['record' => $record])),
                Tables\Actions\Action::make('mark_lulus')
                    ->label('Lulus')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Peserta Lulus')
                    ->modalDescription('Peserta akan ditandai Lulus. Gunakan tombol "Cetak Sertifikat" untuk mengunduh sertifikatnya.')
                    ->action(function (Attendance $record) {
                        $record->update(['status' => 'lulus']);
                        \Illuminate\Support\Facades\Cache::forget('student_data_' . $record->student_id);
                        Notification::make()
                            ->title('Peserta Ditandai Lulus')
                            ->body('Gunakan tombol Cetak Sertifikat untuk mengunduh sertifikat peserta.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cetak_sertifikat')
                    ->label('Cetak Sertifikat')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Attendance $record) {
                        try {
                            $status = $record->status ?? 'gagal';
                            $template = CertificateTemplate::getActiveFor($status);

                            if (!$template) {
                                Notification::make()
                                    ->title('Template Tidak Ditemukan')
                                    ->body('Tidak ada template sertifikat aktif untuk status "' . strtoupper($status) . '". Silakan upload dan aktifkan template di menu Template Sertifikat.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $service = app(WordCertificateService::class);
                            $filePath = $service->generate($record, $template);

                            Notification::make()
                                ->title('Sertifikat Berhasil Dibuat')
                                ->body('File sertifikat untuk ' . $record->name . ' sedang diunduh.')
                                ->success()
                                ->send();

                            $slugName = \Illuminate\Support\Str::slug($record->name, '_');
                            return response()->download(
                                $filePath,
                                "sertifikat_{$record->student_id}_{$slugName}.pdf",
                                ['Content-Type' => 'application/pdf']
                            );
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Membuat Sertifikat')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('mark_gagal')
                    ->label('Gagal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Tidak Lulus')
                    ->action(function (Attendance $record) {
                        $record->update(['status' => 'gagal']);
                        \Illuminate\Support\Facades\Cache::forget('student_data_' . $record->student_id);
                        Notification::make()
                            ->title('Peserta Ditandai Tidak Lulus')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_lulus_bulk')
                        ->label('Tandai Lulus')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Lulus Masal')
                        ->modalDescription('Peserta terpilih akan ditandai Lulus.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $count = $records->count();
                            foreach ($records as $record) {
                                $record->update(['status' => 'lulus']);
                                \Illuminate\Support\Facades\Cache::forget('student_data_' . $record->student_id);
                            }
                            Notification::make()
                                ->title('Selesai')
                                ->body("$count peserta berhasil ditandai lulus.")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('cetak_sertifikat_bulk')
                        ->label('Cetak Sertifikat (ZIP)')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Cetak Sertifikat Masal')
                        ->modalDescription('Sertifikat untuk semua peserta terpilih akan digenerate dan dikemas dalam satu file ZIP untuk diunduh.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            try {
                                // Ambil status dominan dari pilihan (prioritas: lulus)
                                $hasLulus = $records->where('status', 'lulus')->count() > 0;
                                $statusForTemplate = $hasLulus ? 'lulus' : ($records->first()?->status ?? 'gagal');

                                $template = CertificateTemplate::getActiveFor('semua')
                                    ?? CertificateTemplate::getActiveFor($statusForTemplate);

                                if (!$template) {
                                    Notification::make()
                                        ->title('Template Tidak Ditemukan')
                                        ->body('Tidak ada template sertifikat aktif. Silakan upload dan aktifkan template di menu Template Sertifikat.')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                $service = app(WordCertificateService::class);
                                $zipPath = $service->generateBulk($records->load(['group', 'mentor', 'assessment']), $template);

                                Notification::make()
                                    ->title('ZIP Sertifikat Siap')
                                    ->body($records->count() . ' sertifikat berhasil digenerate.')
                                    ->success()
                                    ->send();

                                return response()->download(
                                    $zipPath,
                                    'sertifikat_bulk_' . date('Ymd_His') . '.zip',
                                    ['Content-Type' => 'application/zip']
                                )->deleteFileAfterSend(true);

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Generate ZIP Sertifikat')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\BulkAction::make('mark_gagal_bulk')
                        ->label('Tandai Gagal')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Gagal Masal')
                        ->modalDescription('Apakah Anda yakin ingin menandai tidak lulus peserta yang dipilih?')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $count = $records->count();
                            foreach ($records as $record) {
                                $record->update(['status' => 'gagal']);
                            }
                            Notification::make()
                                ->title('Selesai')
                                ->body("$count peserta berhasil ditandai tidak lulus.")
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
            'point_history' => Pages\ViewPointHistory::route('/{record}/point-history'),
        ];
    }

    public static function canCreate(): bool
    {
        return Group::exists() && Mentor::exists() && (auth()->user()?->can('create_attendance') ?? false); /** @phpstan-ignore-line */
    }
}
