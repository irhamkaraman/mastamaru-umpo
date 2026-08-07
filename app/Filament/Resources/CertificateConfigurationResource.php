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
                            ->label('Background Image (Kosong)')
                            ->image()
                            ->required()
                            ->directory('certificate_templates')
                            ->helperText('Unggah template sertifikat kosong (format gambar). Setelah disimpan, Anda bisa menggunakan fitur "Preview" di halaman Edit.')
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
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->modalHeading('Preview Hasil Sertifikat')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('4xl')
                    ->modalContent(function ($record) {
                        if (!$record->background_image) {
                            return new \Illuminate\Support\HtmlString('<p class="text-danger">Harap upload background image terlebih dahulu.</p>');
                        }

                        $templatePath = storage_path('app/public/' . $record->background_image);
                        if (!file_exists($templatePath)) {
                            return new \Illuminate\Support\HtmlString('<p class="text-danger">File background tidak ditemukan di server.</p>');
                        }

                        $fontPath = public_path('fonts/Roboto-Regular.ttf');
                        if (!file_exists($fontPath)) {
                            return new \Illuminate\Support\HtmlString('<p class="text-danger">Font TTF tidak ditemukan.</p>');
                        }

                        try {
                            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                            $image = $manager->decodePath($templatePath);

                            $writeText = function ($img, $text, $x, $y, $size) use ($fontPath, $record) {
                                if (!$text || $x === null || $y === null) return;
                                $img->text($text, $x, $y, function ($font) use ($fontPath, $record, $size) {
                                    $font->file($fontPath);
                                    $font->size($size);
                                    $font->color($record->text_color ?? '#000000');
                                    $font->align('left');
                                    $font->valign('top');
                                });
                            };

                            $writeText($image, "NAMA PESERTA DUMMY", $record->name_x, $record->name_y, $record->font_size_name);
                            $writeText($image, "21000000", $record->nim_x, $record->nim_y, $record->font_size_nim);
                            
                            $certificateNumber = str_replace('{seq}', '001', $record->number_format);
                            $writeText($image, $certificateNumber, $record->number_x, $record->number_y, $record->font_size_number);
                            
                            if ($record->faculty_x !== null) {
                                $writeText($image, "FAKULTAS DUMMY", $record->faculty_x, $record->faculty_y, $record->font_size_nim);
                            }

                            $dataUri = $image->encode()->toDataUri();
                            
                            return new \Illuminate\Support\HtmlString('<div style="text-align: center;"><img src="' . $dataUri . '" style="max-width: 100%; border-radius: 0.5rem; border: 1px solid #ccc; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"></div><p style="margin-top: 1rem; text-align: center; color: #666; font-size: 0.875rem;">Sesuaikan koordinat X dan Y dengan cara mengedit konfigurasi ini.</p>');
                        } catch (\Exception $e) {
                            return new \Illuminate\Support\HtmlString('<p class="text-danger">Error: ' . $e->getMessage() . '</p>');
                        }
                    }),
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
