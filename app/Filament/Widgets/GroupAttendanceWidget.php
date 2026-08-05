<?php

namespace App\Filament\Widgets;

use App\Models\Group;
use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use App\Models\PresenceSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GroupAttendanceWidget extends BaseWidget
{
    protected static ?string $heading = 'Ringkasan Kehadiran per Kelompok';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';
    
    public ?int $selectedSessionId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Group::query()
                    ->withCount([
                        'attendances',
                        'attendances as present_count' => function (Builder $query) {
                            $query->whereHas('attendanceSubmissions', function (Builder $subQuery) {
                                $subQuery->whereHas('presenceSession'); // Pastikan presence_session ada
                                if ($this->selectedSessionId) {
                                    $subQuery->where('presence_session_id', $this->selectedSessionId);
                                }
                            });
                        }
                    ])
                    ->orderBy('present_count', 'desc')
            )
            ->filters([
                SelectFilter::make('presence_session_id')
                    ->label('Filter Sesi Presensi')
                    ->placeholder('Semua Sesi')
                    ->options(function () {
                        return PresenceSession::orderBy('start_time', 'asc')
                            ->pluck('session_name', 'id')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            $this->selectedSessionId = $data['value'];
                            return $query->withCount([
                                'attendances',
                                'attendances as present_count' => function (Builder $subQuery) use ($data) {
                                    $subQuery->whereHas('attendanceSubmissions', function (Builder $attendanceQuery) use ($data) {
                                        $attendanceQuery->whereHas('presenceSession'); // Pastikan presence_session ada
                                        $attendanceQuery->where('presence_session_id', $data['value']);
                                    });
                                }
                            ]);
                        }
                        $this->selectedSessionId = null;
                        return $query;
                    })
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kelompok')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->default('Tidak ada nama'),

                TextColumn::make('attendances_count')
                    ->label('Total Peserta')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('present_count')
                    ->label('Hadir')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('absent_count')
                    ->label('Tidak Hadir')
                    ->getStateUsing(function (Group $record): int {
                        return $record->attendances_count - $record->present_count;
                    })
                    ->badge()
                    ->color('danger'),
                    
                TextColumn::make('session_info')
                    ->label('Sesi Aktif')
                    ->getStateUsing(function (Group $record): string {
                        if ($this->selectedSessionId) {
                            $session = PresenceSession::find($this->selectedSessionId);
                            return $session ? $session->session_name : 'Sesi tidak ditemukan';
                        }
                        return 'Semua Sesi';
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('attendance_percentage')
                    ->label('Persentase Kehadiran')
                    ->getStateUsing(function (Group $record): string {
                        if ($record->attendances_count == 0) {
                            return '0%';
                        }
                        $percentage = ($record->present_count / $record->attendances_count) * 100;
                        return number_format($percentage, 1) . '%';
                    })
                    ->badge()
                    ->color(function (Group $record): string {
                        if ($record->attendances_count == 0) {
                            return 'gray';
                        }
                        $percentage = ($record->present_count / $record->attendances_count) * 100;
                        if ($percentage >= 80) {
                            return 'success';
                        } elseif ($percentage >= 60) {
                            return 'warning';
                        } else {
                            return 'danger';
                        }
                    }),

                BadgeColumn::make('status')
                    ->label('Status Kelompok')
                    ->getStateUsing(function (Group $record): string {
                        if ($record->attendances_count == 0) {
                            return 'Kosong';
                        }
                        $percentage = ($record->present_count / $record->attendances_count) * 100;
                        if ($percentage >= 80) {
                            return 'Sangat Aktif';
                        } elseif ($percentage >= 60) {
                            return 'Aktif';
                        } elseif ($percentage >= 40) {
                            return 'Kurang Aktif';
                        } else {
                            return 'Tidak Aktif';
                        }
                    })
                    ->colors([
                        'success' => 'Sangat Aktif',
                        'primary' => 'Aktif',
                        'warning' => 'Kurang Aktif',
                        'danger' => 'Tidak Aktif',
                        'gray' => 'Kosong',
                    ]),
            ])
            ->defaultPaginationPageOption(10)
            ->poll('60s'); // Refresh setiap 1 menit
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }
}
