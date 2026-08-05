<?php

namespace App\Filament\Resources\MentorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Data Peserta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form tidak digunakan karena hanya menampilkan data
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
                        return \App\Models\Attendance::distinct()
                            ->pluck('faculty', 'faculty')
                            ->filter()
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('study_program')
                    ->label('Program Studi')
                    ->options(function () {
                        return \App\Models\Attendance::distinct()
                            ->pluck('study_program', 'study_program')
                            ->filter()
                            ->toArray();
                    }),
            ])
            ->headerActions([
                // Tidak ada aksi header karena hanya menampilkan data
            ])
            ->actions([
                // Tidak ada aksi karena pengelolaan data dilakukan di AttendanceResource
            ])
            ->bulkActions([
                // Tidak ada bulk action karena hanya menampilkan data
            ]);
    }
}
