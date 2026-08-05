<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresenceSessionResource\Pages;
use App\Filament\Resources\PresenceSessionResource\RelationManagers;
use App\Models\PresenceSession;
use App\Exports\PresenceSessionExport;
use App\Exports\AllPresenceSessionsExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class PresenceSessionResource extends Resource
{
    protected static ?string $model = PresenceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Sesi Presensi';

    protected static ?string $modelLabel = 'Sesi Presensi';

    protected static ?string $pluralModelLabel = 'Sesi Presensi';

    protected static ?string $navigationGroup = 'Manajemen Presensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('session_name')
                    ->label('Nama Sesi')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, Set $set) {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Slug akan dibuat otomatis dari nama sesi'),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Waktu Mulai')
                    ->required()
                    ->before('end_time'),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Waktu Selesai')
                    ->required()
                    ->after('start_time'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
                Forms\Components\TextInput::make('session_code')
                    ->label('Kode Sesi')
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Kode akan dibuat otomatis'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount('attendanceSubmissions'))
            ->columns([
                Tables\Columns\TextColumn::make('session_name')
                    ->label('Nama Sesi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Waktu Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('session_code')
                    ->label('Kode Sesi')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode sesi disalin!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('attendance_submissions_count')
                    ->label('Peserta Presensi')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all')
                    ->label('Export Semua Data Presensi')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn () => auth()->user()?->can('export', \App\Models\PresenceSession::class) ?? false) /** @phpstan-ignore-line */
                    ->action(function () {
                        $fileName = 'semua-data-presensi-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

                        Notification::make()
                            ->title('Export Semua Data Berhasil')
                            ->body('Semua data presensi dari semua sesi berhasil diexport dalam satu file Excel.')
                            ->success()
                            ->send();

                        return Excel::download(new AllPresenceSessionsExport(), $fileName);
                    })
                    ->tooltip('Download semua data presensi dari semua sesi dalam satu file Excel'),
            ])
            ->actions([
                Tables\Actions\Action::make('export')
                    ->label('Export Data Presensi')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn () => auth()->user()?->can('export', \App\Models\PresenceSession::class) ?? false) /** @phpstan-ignore-line */
                    ->action(function (PresenceSession $record) {
                        $fileName = 'data-presensi-' . \Illuminate\Support\Str::slug($record->session_name) . '-' . now()->format('Y-m-d-H-i-s') . '.xlsx';

                        Notification::make()
                            ->title('Export Berhasil')
                            ->body('Data presensi untuk sesi "' . $record->session_name . '" berhasil diexport.')
                            ->success()
                            ->send();

                        return Excel::download(new PresenceSessionExport($record), $fileName);
                    })
                    ->tooltip('Download data presensi dalam format Excel'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttendanceSubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresenceSessions::route('/'),
            'create' => Pages\CreatePresenceSession::route('/create'),
            'edit' => Pages\EditPresenceSession::route('/{record}/edit'),
        ];
    }
}
