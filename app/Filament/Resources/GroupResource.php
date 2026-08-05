<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use App\Imports\GroupImport;
use App\Exports\GroupTemplateExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Kelompok';

    protected static ?string $pluralModelLabel = 'Kelompok';

    protected static ?string $modelLabel = 'Kelompok';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kelompok')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                        if ($operation !== 'create') {
                            return;
                        }

                        $slug = \Illuminate\Support\Str::slug($state);
                        // Hapus angka dan karakter spesial, hanya biarkan huruf dan tanda (-)
                        $slug = preg_replace('/[^a-zA-Z\-]/', '', $slug);
                        // Hapus tanda (-) berlebihan
                        $slug = preg_replace('/-+/', '-', $slug);
                        // Hapus tanda (-) di awal dan akhir
                        $slug = trim($slug, '-');

                        $set('slug', $slug);
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelompok')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mentors.name')
                    ->label('Nama Pendamping')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->placeholder('Tidak ada pendamping')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendances_count')
                    ->label('Jumlah Peserta')
                    ->counts('attendances')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadTemplate')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn () => auth()->user()?->can('downloadTemplate', \App\Models\Group::class) ?? false) /** @phpstan-ignore-line */
                    ->action(function () {
                        return Excel::download(new GroupTemplateExport, 'template-kelompok.xlsx');
                    }),
                Tables\Actions\Action::make('importExcel')
                    ->label('Import Excel')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('primary')
                    ->visible(fn () => auth()->user()?->can('import', \App\Models\Group::class) ?? false) /** @phpstan-ignore-line */
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->maxSize(5120) // 5MB
                            ->helperText('Format file: .xlsx atau .xls (maksimal 5MB)')
                            ->disk('local')
                            ->directory('imports'),
                    ])
                    ->action(function (array $data) {
                        try {
                            $import = new GroupImport();
                            Excel::import($import, $data['file']);
                            
                            $failures = $import->failures();
                            $errors = $import->errors();
                            
                            if ($failures->isNotEmpty() || $errors->isNotEmpty()) {
                                $errorMessages = [];
                                
                                foreach ($failures as $failure) {
                                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                                }
                                
                                foreach ($errors as $error) {
                                    $errorMessages[] = $error;
                                }
                                
                                Notification::make()
                                    ->title('Import Berhasil dengan Peringatan')
                                    ->body('Beberapa data tidak dapat diimport: ' . implode('; ', array_slice($errorMessages, 0, 3)) . (count($errorMessages) > 3 ? '...' : ''))
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Import Berhasil')
                                    ->body('Data kelompok berhasil diimport.')
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
                    ->modalHeading('Import Data Kelompok')
                    ->modalDescription('Upload file Excel untuk mengimport data kelompok. Pastikan format sesuai dengan template yang disediakan.')
                    ->modalSubmitActionLabel('Import')
                    ->modalCancelActionLabel('Batal'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
