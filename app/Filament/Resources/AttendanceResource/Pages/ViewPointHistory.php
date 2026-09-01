<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\AttendanceSubmission;
use App\Services\ScoreCalculationService;
use Filament\Resources\Pages\ViewRecord;

class ViewPointHistory extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.view-point-history';

    public array $matrix = [];

    public $assessment = null;

    public $submissions = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->matrix = ScoreCalculationService::getStudentPresenceMatrix($this->record->id);
        $this->assessment = ScoreCalculationService::recalculateForStudent($this->record->id);
        $this->submissions = AttendanceSubmission::where('student_id', $this->record->id)
            ->with(['presenceSession', 'mentor'])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    public function getTitle(): string
    {
        return 'Riwayat Poin & Nilai: '.$this->record->name.' ('.$this->record->student_id.')';
    }

    public function getBreadcrumbs(): array
    {
        return [
            AttendanceResource::getUrl('index') => 'Peserta',
            '#' => 'Riwayat Poin',
        ];
    }
}
