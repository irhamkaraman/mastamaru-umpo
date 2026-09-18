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
            ->filters([
                Tables\Filters\SelectFilter::make('faculty')
                    ->label('Fakultas')
                    ->options(fn () => Attendance::select('faculty')->distinct()->whereNotNull('faculty')->pluck('faculty', 'faculty')->toArray())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('study_program')
                    ->label('Prodi')
                    ->options(fn () => Attendance::select('study_program')->distinct()->whereNotNull('study_program')->pluck('study_program', 'study_program')->toArray())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Kelompok')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->emptyStateHeading('Tidak ada peserta gagal');
    }
}
