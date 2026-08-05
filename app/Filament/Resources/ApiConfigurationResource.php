<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiConfigurationResource\Pages;
use App\Filament\Resources\ApiConfigurationResource\RelationManagers;
use App\Models\ApiConfiguration;
use Filament\Forms;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiConfigurationResource extends Resource
{
    protected static ?string $model = ApiConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?string $navigationGroup = 'Integrasi API';
    
    protected static ?string $navigationLabel = 'Konfigurasi API';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Utama')
                    ->description('Pengaturan dasar untuk menghubungi API pihak ketiga.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Konfigurasi')
                            ->placeholder('Misal: API Mahasiswa UMPO')
                            ->helperText('Beri nama yang jelas untuk konfigurasi API ini.')
                            ->required(),
                        TextInput::make('endpoint')
                            ->label('URL Endpoint')
                            ->placeholder('https://api.umpo.ac.id/students')
                            ->helperText('Alamat URL lengkap ke API tujuan.')
                            ->required(),
                        Select::make('method')
                            ->label('Metode HTTP')
                            ->options([
                                'GET' => 'GET',
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                                'DELETE' => 'DELETE',
                            ])->default('GET')
                            ->helperText('Pilih metode request yang dibutuhkan.')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Matikan jika konfigurasi ini tidak digunakan lagi.')
                            ->default(true),
                    ])->columns(2),

                Section::make('Parameter & Headers')
                    ->description('Konfigurasi tambahan untuk otentikasi dan parameter pencarian.')
                    ->schema([
                        KeyValue::make('headers')
                            ->label('Headers Tambahan')
                            ->keyLabel('Nama Header (contoh: Authorization)')
                            ->valueLabel('Nilai Header (contoh: Bearer <token>)')
                            ->helperText('Masukkan pengaturan header tambahan (contoh: X-API-KEY).')
                            ->columnSpanFull(),
                        KeyValue::make('query_params')
                            ->label('Parameter URL (Query Params)')
                            ->keyLabel('Nama Parameter')
                            ->valueLabel('Nilai Parameter')
                            ->helperText('Parameter yang akan ditambahkan ke akhir URL secara otomatis (contoh: ?page=1).')
                            ->columnSpanFull(),
                        Textarea::make('body_payload')
                            ->label('Body Request (Format JSON)')
                            ->helperText('Kosongkan jika menggunakan metode GET. Isi dengan format JSON murni untuk POST/PUT.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pemetaan & Dokumentasi')
                    ->description('Simpan contoh dan aturan pemetaan dari respons API ini.')
                    ->schema([
                        Textarea::make('sample_response')
                            ->label('Contoh Respons (JSON)')
                            ->helperText('Salin dan tempel (Copy-Paste) contoh hasil/response API yang berhasil di sini sebagai referensi pengembangan ke depan.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('name')->searchable(),
                    TextColumn::make('endpoint')->searchable(),
                    TextColumn::make('method'),
                    IconColumn::make('is_active')->boolean(),
                    TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListApiConfigurations::route('/'),
            'create' => Pages\CreateApiConfiguration::route('/create'),
            'edit' => Pages\EditApiConfiguration::route('/{record}/edit'),
        ];
    }
}
