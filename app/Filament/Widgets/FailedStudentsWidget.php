<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class FailedStudentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Daftar Peserta Tidak Lulus';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->where('status', 'gagal')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_id')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('faculty')
                    ->label('Fakultas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('study_program')
                    ->label('Prodi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Kelompok')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('assessment.final_score')
                    ->label('Nilai')
                    ->sortable()
                    ->badge()
                    ->color('danger'),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('Tidak ada peserta gagal');
    }
}
