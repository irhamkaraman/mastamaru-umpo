<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\CertificateTemplate;
use App\Models\Mentor;
use App\Services\WordCertificateService;
use Exception;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

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
                    FileUpload::make('certificate_file')
                        ->label('File Sertifikat PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->required(),
                ])
                ->action(function (array $data, Attendance $record) {
                    $file = $data['certificate_file'];
                    $certDir = storage_path('app/public/certificates');
                    if (! is_dir($certDir)) {
                        mkdir($certDir, 0755, true);
                    }
                    $tempPath = storage_path('app/public/'.$file);
                    $slugName = Str::slug($record->name, '_');
                    $timestamp = time();
                    $targetFilename = $record->student_id.'_sertifikat_'.$timestamp.'.pdf';
                    $targetPath = $certDir.'/'.$targetFilename;
                    $oldFiles = glob($certDir.'/'.$record->student_id.'_sertifikat_*.{docx,pdf}', GLOB_BRACE);
                    if (is_array($oldFiles)) {
                        foreach ($oldFiles as $oldFile) {
                            @unlink($oldFile);
                        }
                    }
                    if (file_exists($tempPath)) {
                        rename($tempPath, $targetPath);
                    }
                    $record->update(['certificate_file' => 'certificates/'.$targetFilename]);
                    Notification::make()
                        ->title('Sertifikat Berhasil Diupload')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('cetak_sertifikat')
                ->label('Cetak Sertifikat')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function (Attendance $record) {
                    try {
                        $status = $record->status ?? 'gagal';
                        $template = CertificateTemplate::getActiveFor($status);
                        if (! $template) {
                            Notification::make()
                                ->title('Template Tidak Ditemukan')
                                ->body('Tidak ada template sertifikat aktif untuk status "'.strtoupper($status).'".')
                                ->warning()
                                ->send();

                            return;
                        }
                        $service = app(WordCertificateService::class);
                        $filePath = $service->generate($record, $template);
                        $slugName = Str::slug($record->name, '_');

                        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                        $mimeType = $extension === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

                        return response()->download(
                            $filePath,
                            "sertifikat_{$record->student_id}_{$slugName}.{$extension}",
                            ['Content-Type' => $mimeType]
                        );
                    } catch (Exception $e) {
                        Notification::make()
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
                ->visible(function (Attendance $record) {
                    $certDir = storage_path('app/public/certificates');
                    $files = glob($certDir.'/'.$record->student_id.'_sertifikat_*.{docx,pdf}', GLOB_BRACE);

                    return is_array($files) && count($files) > 0;
                })
                ->action(function (Attendance $record) {
                    $certDir = storage_path('app/public/certificates');
                    $files = glob($certDir.'/'.$record->student_id.'_sertifikat_*.{docx,pdf}', GLOB_BRACE);
                    if (is_array($files) && count($files) > 0) {
                        foreach ($files as $file) {
                            @unlink($file);
                        }
                        $record->update(['certificate_file' => null]);
                        Notification::make()
                            ->title('Sertifikat Berhasil Dihapus')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
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
        $mentor = Mentor::find($data['mentor_id']);
        $rawBarcode = json_encode([
            'nama' => $data['name'],
            'student_id' => $data['student_id'],
            'mentor' => $mentor ? $mentor->name : '',
        ]);
        $data['raw_barcode'] = $rawBarcode;

        return $data;
    }
}
