<?php

namespace App\Filament\Resources\PresenceSessionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Group;
use App\Models\Mentor;
use App\Models\Attendance;
use Filament\Support\Exceptions\Halt;

class AttendanceSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'attendanceSubmissions';

    protected static ?string $title = 'Data Presensi Peserta';

    protected static ?string $modelLabel = 'Presensi';

    protected static ?string $pluralModelLabel = 'Data Presensi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->label('Peserta')
                    ->relationship('student', 'name')
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $presenceSessionId = request()->route('record');
                                $recordId = request()->route('attendance_submission');

                                $exists = \App\Models\AttendanceSubmission::where('student_id', $value)
                                    ->where('presence_session_id', $presenceSessionId)
                                    ->when($recordId, function ($query) use ($recordId) {
                                        return $query->where('id', '!=', $recordId);
                                    })
                                    ->exists();

                                if ($exists) {
                                    $fail('Peserta ini sudah memiliki data presensi pada sesi ini.');
                                }
                            };
                        }
                    ])
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $student = \App\Models\Attendance::find($state);
                            if ($student) {
                                $set('group_id', $student->group_id);
                                $set('mentor_id', $student->mentor_id);
                            }
                        }
                    }),
                Forms\Components\Hidden::make('group_id'),
                Forms\Components\Hidden::make('mentor_id'),
                Forms\Components\DateTimePicker::make('submitted_at')
                    ->label('Waktu Presensi')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ])
                    ->required()
                    ->default('hadir'),
                Forms\Components\Select::make('submission_method')
                    ->label('Metode Presensi')
                    ->options([
                        'qr_code' => 'QR Code',
                        'manual' => 'Manual',
                        'barcode' => 'Barcode',
                    ])
                    ->required()
                    ->default('manual'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.student_id')
                    ->label('NIM/ID Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Kelompok')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mentor.name')
                    ->label('Pendamping')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student.faculty')
                    ->label('Fakultas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('student.study_program')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Waktu Presensi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'terlambat',
                        'info' => 'izin',
                        'danger' => 'sakit',
                    ]),
                Tables\Columns\TextColumn::make('submission_method')
                    ->label('Metode')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('student.faculty')
                    ->options(function () {
                        return Attendance::whereNotNull('faculty')
                            ->where('faculty', '!=', '')
                            ->distinct()
                            ->pluck('faculty', 'faculty')
                            ->toArray();
                    })
                    ->label('Fakultas')
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('student', fn(Builder $query) => $query->where('faculty', $value))
                        );
                    }),
                Tables\Filters\SelectFilter::make('student.study_program')
                    ->label('Program Studi')
                    ->searchable()
                    ->options(fn() => Attendance::distinct()->pluck('study_program', 'study_program')->toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('student', fn(Builder $query) => $query->where('study_program', $value))
                        );
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ]),
                Tables\Filters\SelectFilter::make('submission_method')
                    ->label('Metode Presensi')
                    ->options([
                        'qr_code' => 'QR Code',
                        'manual' => 'Manual',
                        'barcode' => 'Barcode',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Presensi')
                    ->using(function (array $data, string $model): \Illuminate\Database\Eloquent\Model {
                        try {
                            // Tambahkan presence_session_id dari owner record
                            $data['presence_session_id'] = $this->getOwnerRecord()->id;

                            return $model::create($data);
                        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Data Sudah Ada')
                                ->body('Peserta ini sudah memiliki data presensi pada sesi ini. Silakan pilih peserta lain atau edit data yang sudah ada.')
                                ->danger()
                                ->send();

                            // Lempar exception untuk menghentikan proses
                            throw new Halt();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
