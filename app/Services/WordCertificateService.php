<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CertificateTemplate;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class WordCertificateService
{
    /**
     * Daftar semua placeholder yang didukung sistem.
     */
    public static function getSupportedPlaceholders(): array
    {
        return [
            '{{nama}}' => 'Nama lengkap peserta',
            '{{nim}}' => 'NIM / Nomor Induk Mahasiswa',
            '{{nomor_sertifikat}}' => 'Nomor sertifikat yang digenerate otomatis',
            '{{fakultas}}' => 'Nama Fakultas',
            '{{prodi}}' => 'Program Studi',
            '{{kelompok}}' => 'Nama kelompok yang ditugaskan',
            '{{pendamping}}' => 'Nama pendamping (mentor)',
            '{{no_wa}}' => 'Nomor WhatsApp peserta',
            '{{tanggal}}' => 'Tanggal cetak sertifikat (format Indonesia)',
            '{{nilai}}' => 'Nilai kehadiran dalam persen (%)',
            '{{predikat}}' => 'Predikat kelulusan (A / B / C / D)',
            '{{status}}' => 'Status kelulusan (LULUS / TIDAK LULUS)',
            '{{total_poin}}' => 'Total poin kehadiran dari semua sesi',
        ];
    }

    /**
     * Generate sertifikat Word (.docx) untuk satu peserta.
     * Mengembalikan path absolut ke file hasil.
     */
    public function generate(Attendance $attendance, CertificateTemplate $template): string
    {
        $wordFilePath = storage_path('app/public/'.$template->word_file);
        if (! file_exists($wordFilePath)) {
            throw new Exception("File template Word tidak ditemukan: {$wordFilePath}");
        }
        $nomorSertifikat = $template->generateNextNumber();
        $replacements = $this->buildReplacements($attendance, $nomorSertifikat);
        $processor = new TemplateProcessor($wordFilePath);
        foreach ($replacements as $placeholder => $value) {
            $key = str_replace(['{{', '}}'], '', $placeholder);
            try {
                $processor->setValue($key, $value);
            } catch (Exception $e) {
            }
        }
        $outputDir = storage_path('app/public/certificates');
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        $oldFiles = glob($outputDir.'/'.$attendance->student_id.'_sertifikat_*.{docx,pdf}', GLOB_BRACE);
        foreach ($oldFiles as $old) {
            @unlink($old);
        }
        $slugName = Str::slug($attendance->name, '_');
        $docxFileName = "{$attendance->student_id}_sertifikat_{$slugName}.docx";
        $docxOutputPath = $outputDir.'/'.$docxFileName;
        $processor->saveAs($docxOutputPath);

        $attendance->update([
            'certificate_file' => 'certificates/'.$docxFileName,
        ]);

        return $docxOutputPath;
    }

    /**
     * Generate sertifikat untuk banyak peserta dan hasilkan ZIP.
     * Mengembalikan path absolut ke file .zip.
     */
    public function generateBulk(Collection $attendances, ?CertificateTemplate $defaultTemplate = null): string
    {
        $outputDir = storage_path('app/public/certificates');
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        $tempDir = storage_path('app/private/cert_tmp_'.time());
        mkdir($tempDir, 0755, true);
        $generatedFiles = [];
        $templateLulus = CertificateTemplate::getActiveFor('lulus') ?? CertificateTemplate::getActiveFor('semua');
        $templateGagal = CertificateTemplate::getActiveFor('gagal') ?? CertificateTemplate::getActiveFor('semua');
        foreach ($attendances as $attendance) {
            $template = $defaultTemplate;
            if (! $template) {
                $status = $attendance->status ?? 'gagal';
                $template = $status === 'lulus' ? $templateLulus : $templateGagal;
            }
            if (! $template) {
                continue;
            }
            $wordFilePath = storage_path('app/public/'.$template->word_file);
            if (! file_exists($wordFilePath)) {
                continue;
            }
            $nomorSertifikat = $template->generateNextNumber();
            $replacements = $this->buildReplacements($attendance, $nomorSertifikat);
            $processor = new TemplateProcessor($wordFilePath);
            foreach ($replacements as $placeholder => $value) {
                $key = str_replace(['{{', '}}'], '', $placeholder);
                try {
                    $processor->setValue($key, $value);
                } catch (Exception $e) {
                }
            }
            $slugName = Str::slug($attendance->name, '_');
            $docxFileName = "{$attendance->student_id}_sertifikat_{$slugName}.docx";
            $docxFilePath = $tempDir.'/'.$docxFileName;
            $processor->saveAs($docxFilePath);
            
            $attendance->update([
                'certificate_file' => 'certificates/'.$docxFileName,
            ]);
            $generatedFiles[] = ['path' => $docxFilePath, 'name' => $docxFileName];
        }
        if (empty($generatedFiles)) {
            @rmdir($tempDir);
            throw new Exception('Tidak ada sertifikat yang dapat di-generate (Mungkin template untuk status tersebut belum diatur atau file Word tidak ditemukan).');
        }
        $zipPath = storage_path('app/public/certificates/sertifikat_bulk_'.date('Ymd_His').'.zip');
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new Exception('Gagal membuat file ZIP.');
        }
        foreach ($generatedFiles as $file) {
            $zip->addFile($file['path'], $file['name']);
        }
        $zip->close();
        foreach ($generatedFiles as $file) {
            @unlink($file['path']);
        }
        @rmdir($tempDir);

        return $zipPath;
    }

    /**
     * Bangun array replacement placeholder → nilai untuk satu peserta.
     */
    private function buildReplacements(Attendance $attendance, string $nomorSertifikat): array
    {
        if (! $attendance->relationLoaded('group')) {
            $attendance->load('group');
        }
        if (! $attendance->relationLoaded('mentor')) {
            $attendance->load('mentor');
        }
        if (! $attendance->relationLoaded('assessment')) {
            $attendance->load('assessment');
        }
        $assessment = $attendance->assessment;

        return [
            '{{nama}}' => $attendance->name,
            '{{nim}}' => $attendance->student_id,
            '{{nomor_sertifikat}}' => $nomorSertifikat,
            '{{fakultas}}' => $attendance->faculty ?? '-',
            '{{prodi}}' => $attendance->study_program ?? '-',
            '{{kelompok}}' => $attendance->group?->name ?? '-',
            '{{pendamping}}' => $attendance->mentor?->name ?? '-',
            '{{no_wa}}' => $attendance->phone_number ?? '-',
            '{{tanggal}}' => now()->locale('id')->isoFormat('D MMMM YYYY'),
            '{{nilai}}' => $assessment ? $assessment->attendance_score.'%' : '0%',
            '{{predikat}}' => $assessment ? strtoupper($assessment->grade) : 'D',
            '{{status}}' => strtoupper($attendance->status ?? 'TIDAK LULUS'),
            '{{total_poin}}' => $assessment ? (string) $assessment->total_presence_points : '0',
        ];
    }

    /**
     * Deteksi placeholder {{...}} yang ada di dalam file .docx.
     * Berguna untuk preview di admin panel.
     */
    public function detectPlaceholders(string $wordFilePath): array
    {
        if (! file_exists($wordFilePath)) {
            return [];
        }
        try {
            $processor = new TemplateProcessor($wordFilePath);
            $variables = $processor->getVariables();
            $supported = array_keys(self::getSupportedPlaceholders());
            $supportedKeys = array_map(fn ($p) => str_replace(['{{', '}}'], '', $p), $supported);
            $found = [];
            foreach ($variables as $v) {
                if (in_array($v, $supportedKeys)) {
                    $found[] = '{{'.$v.'}}';
                }
            }

            return array_unique($found);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Buat file .docx template contoh yang bisa diunduh admin.
     */
    public function createSampleTemplate(): string
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord;
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
        ]);
        $titleStyle = ['bold' => true, 'size' => 20, 'name' => 'Times New Roman', 'color' => '1a3a5c'];
        $headStyle = ['bold' => true, 'size' => 14, 'name' => 'Times New Roman', 'color' => '1a3a5c'];
        $bodyStyle = ['size' => 12, 'name' => 'Times New Roman'];
        $fieldStyle = ['bold' => true, 'size' => 14, 'name' => 'Times New Roman', 'underline' => 'single'];
        $centerPara = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 40];
        $normalPara = ['spaceAfter' => 20];
        $section->addText('UNIVERSITAS MUHAMMADIYAH PONOROGO', $titleStyle, $centerPara);
        $section->addText('PANITIA MASTAMARU 2026', $headStyle, $centerPara);
        $section->addTextBreak(1);
        $section->addText('SERTIFIKAT', ['bold' => true, 'size' => 26, 'name' => 'Times New Roman', 'color' => '0d3b6e'], $centerPara);
        $section->addText('MASA TA\'ARUF MAHASISWA BARU (MASTAMARU) 2026', ['size' => 11, 'name' => 'Times New Roman', 'color' => '555555'], $centerPara);
        $section->addText('Nomor: ${nomor_sertifikat}', ['size' => 11, 'name' => 'Times New Roman', 'italic' => true], $centerPara);
        $section->addTextBreak(1);
        $section->addText('Diberikan kepada:', $bodyStyle, $normalPara);
        $section->addText('${nama}', array_merge($fieldStyle, ['size' => 20, 'color' => '1a3a5c']), $centerPara);
        $section->addText('NIM: ${nim}', $bodyStyle, $centerPara);
        $section->addText('Fakultas: ${fakultas}', $bodyStyle, $centerPara);
        $section->addText('Program Studi: ${prodi}', $bodyStyle, $centerPara);
        $section->addTextBreak(1);
        $section->addText(
            'Yang telah mengikuti kegiatan Masa Ta\'aruf Mahasiswa Baru (MASTAMARU) Universitas Muhammadiyah Ponorogo Tahun 2026 dengan predikat:',
            $bodyStyle,
            $normalPara
        );
        $section->addText('${predikat}  (Nilai: ${nilai})', ['bold' => true, 'size' => 18, 'name' => 'Times New Roman', 'color' => '16a34a'], $centerPara);
        $section->addText('Status: ${status}', ['bold' => true, 'size' => 13, 'name' => 'Times New Roman'], $centerPara);
        $section->addTextBreak(1);
        $section->addText('Kelompok: ${kelompok}   |   Pendamping: ${pendamping}', $bodyStyle, $centerPara);
        $section->addText('Ponorogo, ${tanggal}', $bodyStyle, $normalPara);
        $section->addTextBreak(2);
        $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 50]);
        $table->addRow();
        $col1 = $table->addCell(4000, ['borderSize' => 0]);
        $col1->addText('Ketua Panitia MASTAMARU', $bodyStyle, $centerPara);
        $col1->addTextBreak(3);
        $col1->addText('(____________________)', $bodyStyle, $centerPara);
        $col2 = $table->addCell(4000, ['borderSize' => 0]);
        $col2->addText('Wakil Rektor III UMPO', $bodyStyle, $centerPara);
        $col2->addTextBreak(3);
        $col2->addText('(____________________)', $bodyStyle, $centerPara);
        $section->addTextBreak(2);
        $section->addText(
            '--- Panduan Placeholder: ${nama} ${nim} ${nomor_sertifikat} ${fakultas} ${prodi} ${kelompok} ${pendamping} ${no_wa} ${tanggal} ${nilai} ${predikat} ${status} ${total_poin} ---',
            ['size' => 7, 'name' => 'Courier New', 'color' => 'cccccc'],
            $centerPara
        );
        $dir = storage_path('app/public/certificate_samples');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $outputPath = $dir.'/template-sertifikat-contoh.docx';
        $phpWord->save($outputPath, 'Word2007');

        return $outputPath;
    }
}
