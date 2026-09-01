<?php

namespace App\Filament\Resources;

use App\Exports\MentorDataExport;
use App\Exports\MentorTemplateExport;
use App\Filament\Resources\MentorResource\Pages;
use App\Filament\Resources\MentorResource\RelationManagers;
use App\Imports\MentorImport;
use App\Models\Group;
use App\Models\Mentor;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class MentorResource extends Resource
{
    protected static ?string $model = Mentor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Pendamping';

    protected static ?string $modelLabel = 'Pendamping';

    protected static ?string $pluralModelLabel = 'Pendamping';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'name')
                    ->required()
                    ->label('Kelompok')
                    ->searchable()
                    ->options(function () {
                        $groups = Group::all();
                        if ($groups->isEmpty()) {
                            throw new Exception('Tidak ada kelompok yang tersedia. Silakan buat kelompok terlebih dahulu.');
                        }

                        return $groups->pluck('name', 'id');
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Pendamping'),
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('NIM'),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(20)
                    ->label('Nomor WhatsApp / Telp')
                    ->placeholder('Contoh: 081234567890')
                    ->helperText('Nomor yang dapat dihubungi oleh peserta kelompok'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->label('Kata Sandi')
                    ->dehydrateStateUsing(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('raw_password', $state);
                        }

                        return Hash::make($state);
                    })
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('raw_password', $state);
                        }
                    }),
                Forms\Components\Hidden::make('raw_password')
                    ->dehydrated(),
                Forms\Components\TextInput::make('password_hint')
                    ->label('Petunjuk Kata Sandi (Kata Sandi Asli)')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->raw_password)
                    ->helperText('Ini menampilkan kata sandi yang tidak terenkripsi untuk referensi administrator')
                    ->visible(fn (string $context): bool => $context === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn () => (auth()->user()?->can('export', Mentor::class) ?? false) && Group::exists()) /** @phpstan-ignore-line */
                    ->action(function ($livewire) {
                        try {
                            ini_set('memory_limit', '2048M');
                            ini_set('max_execution_time', 600);
                            $filters = $livewire->tableFilters ?? [];
                            $filename = 'data-pendamping-'.date('Y-m-d-H-i-s').'.xlsx';

                            return Excel::download(new MentorDataExport($filters), $filename);
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Export Gagal')
                                ->body('Terjadi kesalahan saat export: '.$e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    })
                    ->tooltip('Export data pendamping ke format Excel (.xlsx)'),
                Tables\Actions\Action::make('downloadTemplate')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn () => (auth()->user()?->can('downloadTemplate', Mentor::class) ?? false) && Group::exists()) /** @phpstan-ignore-line */
                    ->action(function () {
                        return Excel::download(new MentorTemplateExport, 'template-pendamping.xlsx');
                    }),
                Tables\Actions\Action::make('importExcel')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->visible(fn () => (auth()->user()?->can('import', Mentor::class) ?? false) && Group::exists()) /** @phpstan-ignore-line */
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->disk('public')
                            ->directory('imports')
                            ->helperText('Upload file Excel dengan format: Nama Kelompok, Nama Pendamping, NIM, Nomor WhatsApp / Telp, Kata Sandi'),
                    ])
                    ->action(function (array $data) {
                        try {
                            $import = new MentorImport;
                            Excel::import($import, storage_path('app/public/'.$data['file']));
                            $importedCount = $import->getImportedCount();
                            $skippedCount = $import->getSkippedCount();
                            $failures = $import->failures();
                            $errors = $import->errors();
                            if ($importedCount > 0) {
                                $message = "Berhasil mengimpor {$importedCount} pendamping.";
                                if ($skippedCount > 0) {
                                    $message .= " {$skippedCount} data dilewati.";
                                }
                                if (! empty($failures) || ! empty($errors)) {
                                    Notification::make()
                                        ->title('Import Selesai dengan Peringatan')
                                        ->body($message)
                                        ->warning()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Import Berhasil')
                                        ->body($message)
                                        ->success()
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->title('Import Gagal')
                                    ->body('Tidak ada data yang berhasil diimpor. Periksa format file dan pastikan data valid.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Terjadi kesalahan: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Pendamping'),
                Tables\Columns\TextColumn::make('student_id')
                    ->searchable()
                    ->sortable()
                    ->label('NIM'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->label('No. Telp / WA')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('group.name')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Kelompok'),
                Tables\Columns\TextColumn::make('attendances_count')
                    ->counts('attendances')
                    ->label('Jumlah Peserta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('raw_password')
                    ->label('Kata Sandi Asli')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->actions([
                Tables\Actions\Action::make('presence_log')
                    ->label('Log Presensi & Poin')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('info')
                    ->modalHeading(fn (Mentor $record) => 'Log Presensi yang Dicatat oleh Mentor: '.$record->name.' ('.$record->student_id.')')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (Mentor $record) {
                        $participants = \App\Models\Attendance::where('group_id', $record->group_id)
                            ->with(['attendanceSubmissions' => function ($q) {
                                $q->with('presenceSession')->orderBy('submitted_at', 'desc');
                            }])
                            ->get();

                        return view('filament.modals.mentor-point-history', [
                            'mentor' => $record,
                            'participants' => $participants,
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMentors::route('/'),
            'create' => Pages\CreateMentor::route('/create'),
            'edit' => Pages\EditMentor::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Group::exists();
    }
}
