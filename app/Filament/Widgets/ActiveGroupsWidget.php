<?php

namespace App\Filament\Widgets;

use App\Models\Group;
use App\Models\AttendanceSubmission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ActiveGroupsWidget extends ChartWidget
{
    protected static ?string $heading = 'Kelompok Aktif Presensi';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Ambil data kelompok dengan jumlah kehadiran
        $groupStats = Group::select('groups.id', 'groups.name')
            ->leftJoin('attendances', 'groups.id', '=', 'attendances.group_id')
            ->leftJoin('attendance_submissions', 'attendances.id', '=', 'attendance_submissions.student_id')
            ->groupBy('groups.id', 'groups.name')
            ->selectRaw('COUNT(attendance_submissions.id) as total_submissions')
            ->orderBy('total_submissions', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
        ];

        foreach ($groupStats as $index => $group) {
            $labels[] = $group->name ?: 'Kelompok ' . $group->id;
            $data[] = $group->total_submissions;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kehadiran',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}