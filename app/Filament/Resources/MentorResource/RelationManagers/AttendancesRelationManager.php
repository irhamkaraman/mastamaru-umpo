<?php

namespace App\Filament\Resources\MentorResource\RelationManagers;

use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Data Peserta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_id')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('faculty')
                    ->label('Fakultas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('study_program')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->label('Kode Unik')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada kode unik'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('faculty')
                    ->label('Fakultas')
                    ->options(function () {
                        return Attendance::distinct()
                            ->pluck('faculty', 'faculty')
                            ->filter()
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('study_program')
                    ->label('Program Studi')
                    ->options(function () {
                        return Attendance::distinct()
                            ->pluck('study_program', 'study_program')
                            ->filter()
                            ->toArray();
                    }),
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}
