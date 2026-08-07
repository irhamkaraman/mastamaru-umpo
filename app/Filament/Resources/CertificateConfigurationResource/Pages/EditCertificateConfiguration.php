<?php

namespace App\Filament\Resources\CertificateConfigurationResource\Pages;

use App\Filament\Resources\CertificateConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificateConfiguration extends EditRecord
{
    protected static string $resource = CertificateConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('preview')
                ->label('Preview Sertifikat')
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
                        
                        return new \Illuminate\Support\HtmlString('<div style="text-align: center;"><img src="' . $dataUri . '" style="max-width: 100%; border-radius: 0.5rem; border: 1px solid #ccc; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"></div><p style="margin-top: 1rem; text-align: center; color: #666; font-size: 0.875rem;">Sesuaikan koordinat X dan Y pada form, lalu simpan (Save) untuk melihat perubahan di sini.</p>');
                    } catch (\Exception $e) {
                        return new \Illuminate\Support\HtmlString('<p class="text-danger">Error: ' . $e->getMessage() . '</p>');
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
