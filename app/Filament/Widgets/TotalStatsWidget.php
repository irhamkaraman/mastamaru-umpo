<?php

namespace App\Filament\Widgets;

use App\Models\Group;
use App\Models\Attendance;
use App\Models\Mentor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalGroups = Group::count();
        $totalStudents = Attendance::count();
        $totalMentors = Mentor::count();

        return [
            Stat::make('Total Kelompok', $totalGroups)
                ->description('Kelompok terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('Total Peserta', $totalStudents)
                ->description('Peserta terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
            
            Stat::make('Total Pendamping', $totalMentors)
                ->description('Pendamping aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning')
                ->chart([3, 2, 5, 3, 6, 4, 7]),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }

    public function getDisplayName(): string
    {
        return 'Statistik Total';
    }
}