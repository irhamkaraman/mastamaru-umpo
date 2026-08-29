<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateTemplateResource\Pages;
use App\Models\CertificateTemplate;
use App\Services\WordCertificateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificateTemplateResource extends Resource
{
    protected static ?string $model = CertificateTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Konfigurasi';

    protected static ?string $navigationLabel = 'Template Sertifikat';

    protected static ?string $modelLabel = 'Template Sertifikat';

    protected static ?string $pluralModelLabel = 'Template Sertifikat';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Template')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Sertifikat MASTAMARU 2026'),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(2)
                            ->placeholder('Deskripsi singkat template ini'),

                        Forms\Components\Select::make('applies_to')
                            ->label('Berlaku Untuk')
                            ->options([
                                'lulus'  => 'Peserta Lulus',
                                'gagal'  => 'Peserta Tidak Lulus',
                                'semua'  => 'Semua Peserta',
                            ])
                            ->default('lulus')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Hanya satu template yang akan digunakan saat cetak. Template aktif dengan tanggal terbaru yang dipilih.')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('File & Penomoran')
                    ->schema([
                        Forms\Components\FileUpload::make('word_file')
                            ->label('File Template Word (.docx)')
                            ->required()
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/msword',
                            ])
                            ->disk('public')
                            ->directory('certificate_templates')
                            ->maxSize(10240)
                            ->helperText('Upload file .docx yang berisi placeholder seperti ${nama}, ${nim}, ${nomor_sertifikat}, dll.'),

                        Forms\Components\TextInput::make('number_format')
                            ->label('Format Nomor Sertifikat')
                            ->default('CERT/{seq}/MASTAMARU/2026')
                            ->required()
                            ->helperText('Gunakan {seq} sebagai pengganti nomor urut. Contoh: CERT/{seq}/UMPO/2026')
                            ->placeholder('CERT/{seq}/MASTAMARU/2026'),

                        Forms\Components\TextInput::make('current_sequence')
                            ->label('Nomor Urut Terakhir')
                            ->numeric()
                            ->default(0)
                            ->helperText('Reset ke 0 untuk memulai penomoran dari awal.'),
                    ])->columns(3),

                Forms\Components\Section::make('Panduan Placeholder')
                    ->schema([
                        Forms\Components\Placeholder::make('placeholder_guide')
                            ->label('')
                            ->content(function () {
                                $placeholders = WordCertificateService::getSupportedPlaceholders();
                                $rows = '';
                                foreach ($placeholders as $ph => $desc) {
                                    $key = str_replace(['{{', '}}'], '', $ph);
                                    $rows .= "<tr>
                                        <td style='padding:6px 12px;font-family:monospace;font-weight:600;color:#1a3a5c;white-space:nowrap;'>\${$key}</td>
                                        <td style='padding:6px 12px;color:#444;'>{$desc}</td>
                                    </tr>";
                                }
                                return new \Illuminate\Support\HtmlString("
                                    <div style='font-size:13px;'>
                                        <p style='margin-bottom:8px;color:#555;'>Gunakan placeholder berikut di dalam file Word Anda. PhpWord menggunakan format <code style='background:#f3f4f6;padding:2px 6px;border-radius:4px;font-weight:700;'>\${nama}</code> (kurung kurawal tunggal tanpa spasi):</p>
                                        <table style='border-collapse:collapse;width:100%;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;'>
                                            <thead><tr style='background:#1a3a5c;color:white;'>
                                                <th style='padding:8px 12px;text-align:left;font-weight:700;'>Placeholder</th>
                                                <th style='padding:8px 12px;text-align:left;font-weight:700;'>Deskripsi</th>
                                            </tr></thead>
                                            <tbody>{$rows}</tbody>
                                        </table>
                                        <p style='margin-top:8px;color:#888;font-size:12px;'>⚠️ Pastikan placeholder ditulis persis tanpa spasi ekstra. Cukup ketik di Word: <code>\${nama}</code></p>
                                    </div>
                                ");
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('applies_to')
                    ->label('Berlaku Untuk')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lulus'  => 'success',
                        'gagal'  => 'danger',
                        'semua'  => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'lulus'  => 'Peserta Lulus',
                        'gagal'  => 'Peserta Tidak Lulus',
                        'semua'  => 'Semua Peserta',
                        default  => $state,
                    }),
                Tables\Columns\TextColumn::make('number_format')
                    ->label('Format Nomor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_sequence')
                    ->label('Sertifikat Tercetak')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_sample_template')
                    ->label('Download Template Contoh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function () {
                        try {
                            $service = app(WordCertificateService::class);
                            $path = $service->createSampleTemplate();

                            return response()->download($path, 'template-sertifikat-mastamaru-2026.docx', [
                                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ]);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal membuat template')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->tooltip('Unduh contoh file template Word dengan semua placeholder tersedia'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (CertificateTemplate $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn (CertificateTemplate $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (CertificateTemplate $record) => $record->is_active ? 'warning' : 'success')
                    ->action(function (CertificateTemplate $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Template Diaktifkan' : 'Template Dinonaktifkan')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('preview_placeholders')
                    ->label('Cek Placeholder')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('info')
                    ->modalHeading('Placeholder Terdeteksi di Template')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (CertificateTemplate $record) {
                        $service = app(WordCertificateService::class);
                        $path = storage_path('app/public/' . $record->word_file);
                        $found = $service->detectPlaceholders($path);
                        $supported = WordCertificateService::getSupportedPlaceholders();

                        if (empty($found)) {
                            return new \Illuminate\Support\HtmlString(
                                '<div style="padding:16px;color:#888;">Tidak ada placeholder yang terdeteksi, atau file belum dapat dibaca.</div>'
                            );
                        }

                        $rows = '';
                        foreach ($found as $ph) {
                            $desc = $supported[$ph] ?? 'Placeholder kustom';
                            $key = str_replace(['{{', '}}'], '', $ph);
                            $rows .= "<tr>
                                <td style='padding:8px 14px;font-family:monospace;font-weight:700;color:#1a3a5c;'>\${$key}</td>
                                <td style='padding:8px 14px;color:#555;'>{$desc}</td>
                                <td style='padding:8px 14px;text-align:center;'>✅</td>
                            </tr>";
                        }

                        return new \Illuminate\Support\HtmlString("
                            <div style='padding:16px;'>
                                <table style='width:100%;border-collapse:collapse;border:1px solid #e5e7eb;'>
                                    <thead>
                                        <tr style='background:#1a3a5c;color:white;'>
                                            <th style='padding:8px 14px;text-align:left;'>Placeholder</th>
                                            <th style='padding:8px 14px;text-align:left;'>Keterangan</th>
                                            <th style='padding:8px 14px;'>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>{$rows}</tbody>
                                </table>
                            </div>
                        ");
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (CertificateTemplate $record) {
                        // Hapus file word saat template dihapus
                        $path = storage_path('app/public/' . $record->word_file);
                        if (file_exists($path)) @unlink($path);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCertificateTemplates::route('/'),
            'create' => Pages\CreateCertificateTemplate::route('/create'),
            'edit'   => Pages\EditCertificateTemplate::route('/{record}/edit'),
        ];
    }
}
