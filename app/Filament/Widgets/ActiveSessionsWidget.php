<?php

namespace App\Filament\Widgets;

use App\Models\PresenceSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ActiveSessionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Sesi Presensi Aktif';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PresenceSession::query()
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('session_name')
                    ->label('Nama Sesi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('session_code')
                    ->label('Kode Sesi')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Kode sesi disalin!')
                    ->copyMessageDuration(1500),
                
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('end_time')
                    ->label('Waktu Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('attendance_submissions_count')
                    ->label('Total Kehadiran')
                    ->counts('attendanceSubmissions')
                    ->badge()
                    ->color('success'),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (PresenceSession $record): string {
                        $now = Carbon::now();
                        $start = Carbon::parse($record->start_time);
                        $end = Carbon::parse($record->end_time);
                        
                        if ($now < $start) {
                            return 'Belum Dimulai';
                        } elseif ($now > $end) {
                            return 'Selesai';
                        } else {
                            return 'Berlangsung';
                        }
                    })
                    ->colors([
                        'warning' => 'Belum Dimulai',
                        'success' => 'Berlangsung',
                        'danger' => 'Selesai',
                    ]),
            ])
            ->defaultPaginationPageOption(5)
            ->poll('30s'); // Refresh setiap 30 detik
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25];
    }
}