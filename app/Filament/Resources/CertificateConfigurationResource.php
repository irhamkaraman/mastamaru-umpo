<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateConfigurationResource\Pages;
use App\Filament\Resources\CertificateConfigurationResource\RelationManagers;
use App\Models\CertificateConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificateConfigurationResource extends Resource
{
    protected static ?string $model = CertificateConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template & Format')
                    ->schema([
                        Forms\Components\FileUpload::make('background_image')
                            ->image()
                            ->directory('certificate-templates')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('number_format')
                            ->required()
                            ->maxLength(255)
                            ->default('/PAN/MASTAMARU/UMPO/2026')
                            ->helperText('Gunakan {seq} untuk nomor urut dinamis. Contoh: {seq}/PAN/MASTAMARU/UMPO/2026'),
                        Forms\Components\TextInput::make('current_sequence')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Nomor urut terakhir yang di-generate.'),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Koordinat Posisi Teks (X, Y)')
                    ->description('Tentukan posisi X (Kiri-Kanan) dan Y (Atas-Bawah) dalam satuan pixel dari pojok kiri atas gambar template.')
                    ->schema([
                        Forms\Components\Fieldset::make('Nama Peserta')
                            ->schema([
                                Forms\Components\TextInput::make('name_x')->required()->numeric()->default(100),
                                Forms\Components\TextInput::make('name_y')->required()->numeric()->default(200),
                                Forms\Components\TextInput::make('font_size_name')->required()->numeric()->default(32),
                            ])->columns(3),
                        
                        Forms\Components\Fieldset::make('NIM Peserta')
                            ->schema([
                                Forms\Components\TextInput::make('nim_x')->required()->numeric()->default(100),
                                Forms\Components\TextInput::make('nim_y')->required()->numeric()->default(250),
                                Forms\Components\TextInput::make('font_size_nim')->required()->numeric()->default(24),
                            ])->columns(3),
                            
                        Forms\Components\Fieldset::make('Nomor Sertifikat')
                            ->schema([
                                Forms\Components\TextInput::make('number_x')->required()->numeric()->default(100),
                                Forms\Components\TextInput::make('number_y')->required()->numeric()->default(300),
                                Forms\Components\TextInput::make('font_size_number')->required()->numeric()->default(24),
                            ])->columns(3),
                            
                        Forms\Components\Fieldset::make('Fakultas (Opsional)')
                            ->schema([
                                Forms\Components\TextInput::make('faculty_x')->numeric(),
                                Forms\Components\TextInput::make('faculty_y')->numeric(),
                            ])->columns(2),
                    ]),
                    
                Forms\Components\Section::make('Pengaturan Teks Global')
                    ->schema([
                        Forms\Components\ColorPicker::make('text_color')
                            ->required()
                            ->default('#000000'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('background_image'),
                Tables\Columns\TextColumn::make('number_format')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_sequence')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificateConfigurations::route('/'),
            'create' => Pages\CreateCertificateConfiguration::route('/create'),
            'edit' => Pages\EditCertificateConfiguration::route('/{record}/edit'),
        ];
    }
}
