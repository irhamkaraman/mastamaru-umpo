<?php

namespace App\Filament\Widgets;

use App\Models\PresenceSession;
use App\Models\AttendanceSubmission;
use App\Models\Group;
use App\Models\Attendance;
use App\Models\Mentor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class SystemInfoWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    
    protected function getStats(): array
    {
        // Statistik hari ini
        $todaySubmissions = AttendanceSubmission::whereDate('created_at', today())->count();
        $activeSessions = PresenceSession::where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();
        
        // Kelompok dengan kehadiran tertinggi
        $topGroup = Group::withCount([
            'attendances as present_count' => function ($query) {
                $query->whereHas('attendanceSubmissions');
            }
        ])
        ->orderBy('present_count', 'desc')
        ->first();
        
        // Persentase kehadiran keseluruhan
        $totalStudents = Attendance::count();
        $totalSubmissions = AttendanceSubmission::count();
        $overallPercentage = $totalStudents > 0 ? ($totalSubmissions / $totalStudents) * 100 : 0;
        
        return [
            Stat::make('Presensi Hari Ini', $todaySubmissions)
                ->description('Total kehadiran hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([3, 5, 8, 12, 15, 18, $todaySubmissions]),
            
            Stat::make('Sesi Berlangsung', $activeSessions)
                ->description('Sesi presensi aktif saat ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color($activeSessions > 0 ? 'success' : 'gray'),
            
            Stat::make('Kelompok Terbaik', $topGroup ? ($topGroup->name ?: 'Kelompok ' . $topGroup->id) : 'Belum ada')
                ->description($topGroup ? $topGroup->present_count . ' kehadiran' : 'Tidak ada data')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
            
            Stat::make('Tingkat Kehadiran', number_format($overallPercentage, 1) . '%')
                ->description('Persentase kehadiran keseluruhan')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($overallPercentage >= 80 ? 'success' : ($overallPercentage >= 60 ? 'warning' : 'danger')),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public function getDisplayName(): string
    {
        return 'Informasi Sistem Real-time';
    }
    
    // Refresh widget setiap 30 detik
    protected static ?string $pollingInterval = '30s';
}