<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Mentor;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload_sertifikat')
                ->label('Upload Sertifikat')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('certificate_file')
                        ->label('File Sertifikat PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->required(),
                ])
                ->action(function (array $data, \App\Models\Attendance $record) {
                    $file = $data['certificate_file'];
                    $certDir = storage_path('app/public/certificates');
                    if (!is_dir($certDir)) {
                        mkdir($certDir, 0755, true);
                    }
                    
                    // The file is a temporary uploaded file path in Filament. We need to move it.
                    $tempPath = storage_path('app/public/' . $file);
                    
                    // Generate exact target filename matching the old system format
                    $slugName = \Illuminate\Support\Str::slug($record->name, '_');
                    $timestamp = time();
                    $targetFilename = $record->student_id . '_sertifikat_' . $timestamp . '.pdf';
                    $targetPath = $certDir . '/' . $targetFilename;
                    
                    // Delete old certificates if exist
                    $oldFiles = glob($certDir . '/' . $record->student_id . '_sertifikat_*.pdf');
                    if (is_array($oldFiles)) {
                        foreach ($oldFiles as $oldFile) {
                            @unlink($oldFile);
                        }
                    }
                    
                    if (file_exists($tempPath)) {
                        rename($tempPath, $targetPath);
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Sertifikat Berhasil Diupload')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('cetak_sertifikat')
                ->label('Cetak Sertifikat')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function (\App\Models\Attendance $record) {
                    try {
                        $status = $record->status ?? 'gagal';
                        $template = \App\Models\CertificateTemplate::getActiveFor($status);

                        if (!$template) {
                            \Filament\Notifications\Notification::make()
                                ->title('Template Tidak Ditemukan')
                                ->body('Tidak ada template sertifikat aktif untuk status "' . strtoupper($status) . '".')
                                ->warning()
                                ->send();
                            return;
                        }

                        $service = app(\App\Services\WordCertificateService::class);
                        $filePath = $service->generate($record, $template);

                        $slugName = \Illuminate\Support\Str::slug($record->name, '_');
                        return response()->download(
                            $filePath,
                            "sertifikat_{$record->student_id}_{$slugName}.pdf",
                            ['Content-Type' => 'application/pdf']
                        );
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal Mencetak Sertifikat')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('hapus_sertifikat')
                ->label('Hapus Sertifikat')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(function (\App\Models\Attendance $record) {
                    $certDir = storage_path('app/public/certificates');
                    $files = glob($certDir . '/' . $record->student_id . '_sertifikat_*.pdf');
                    return is_array($files) && count($files) > 0;
                })
                ->action(function (\App\Models\Attendance $record) {
                    $certDir = storage_path('app/public/certificates');
                    $files = glob($certDir . '/' . $record->student_id . '_sertifikat_*.pdf');
                    if (is_array($files) && count($files) > 0) {
                        foreach ($files as $file) {
                            @unlink($file);
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Sertifikat Berhasil Dihapus')
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Sertifikat Tidak Ditemukan')
                            ->warning()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update raw barcode JSON saat edit
        $mentor = Mentor::find($data['mentor_id']);
        $rawBarcode = json_encode([
            'nama' => $data['name'],
            'student_id' => $data['student_id'],
            'mentor' => $mentor ? $mentor->name : ''
        ]);
        
        $data['raw_barcode'] = $rawBarcode;
        
        return $data;
    }
}
