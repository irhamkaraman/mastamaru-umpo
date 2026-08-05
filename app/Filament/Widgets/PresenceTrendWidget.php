<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceSubmission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresenceTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Presensi 7 Hari Terakhir';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Ambil data presensi 7 hari terakhir
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            $count = AttendanceSubmission::whereDate('created_at', $date->toDateString())->count();
            
            return [
                'date' => $date->format('d/m'),
                'count' => $count,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Presensi',
                    'data' => $last7Days->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $last7Days->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}