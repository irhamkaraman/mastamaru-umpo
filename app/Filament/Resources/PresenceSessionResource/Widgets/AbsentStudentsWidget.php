<?php

namespace App\Filament\Resources\PresenceSessionResource\Widgets;

use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AbsentStudentsWidget extends BaseWidget
{
    protected static ?string $heading = 'Peserta yang Tidak Melakukan Presensi';

    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    protected function getTableQuery(): Builder
    {
        $presenceSessionId = $this->getPresenceSessionId();
        
        if (!$presenceSessionId) {
            return Attendance::query()->whereRaw('1 = 0'); // Return empty result
        }
        
        $submittedStudentIds = DB::table('attendance_submissions')
            ->where('presence_session_id', $presenceSessionId)
            ->pluck('student_id');

        return Attendance::query()
            ->whereNotIn('id', $submittedStudentIds)
            ->with(['group', 'mentor']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student_id')
                    ->label('NIM/ID Peserta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('faculty')
                    ->label('Fakultas')
                    ->searchable()
                    ->sortable()
                    ->default('Tidak tersedia'),
                TextColumn::make('study_program')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable()
                    ->default('Tidak tersedia'),
                TextColumn::make('group.name')
                    ->label('Kelompok')
                    ->sortable(),
                TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('faculty')
                    ->label('Fakultas')
                    ->options(function () {
                        return Attendance::query()
                            ->whereNotNull('faculty')
                            ->where('faculty', '!=', '')
                            ->distinct()
                            ->pluck('faculty', 'faculty')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('study_program')
                    ->label('Program Studi')
                    ->options(function () {
                        return Attendance::query()
                            ->whereNotNull('study_program')
                            ->where('study_program', '!=', '')
                            ->distinct()
                            ->pluck('study_program', 'study_program')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                SelectFilter::make('group_id')
                    ->label('Kelompok')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('mentor_id')
                    ->label('Mentor')
                    ->relationship('mentor', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('Semua peserta sudah melakukan presensi')
            ->emptyStateDescription('Tidak ada peserta yang belum melakukan presensi untuk sesi ini.')
            ->emptyStateIcon('heroicon-o-check-circle');
     }

    protected function getPresenceSessionId(): ?int
    {
        return $this->record?->id;
    }
}
