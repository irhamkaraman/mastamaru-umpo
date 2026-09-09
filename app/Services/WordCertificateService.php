<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSubmission;
use App\Models\CertificateTemplate;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Image as ImageStyle;
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
     * Halaman 1: template yang diupload admin (sudah diisi placeholder).
     * Halaman 2: rekap histori presensi + total poin otomatis dengan background MASTAMARU.
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
            } catch (Exception $e) {}
        }

        $outputDir = storage_path('app/public/certificates');
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $oldFiles = glob($outputDir.'/'.$attendance->student_id.'_sertifikat_*.{docx,pdf}', GLOB_BRACE);
        foreach ($oldFiles as $old) {
            @unlink($old);
        }

        $slugName       = Str::slug($attendance->name, '_');
        $docxFileName   = "{$attendance->student_id}_sertifikat_{$slugName}.docx";
        $docxOutputPath = $outputDir.'/'.$docxFileName;
        $processor->saveAs($docxOutputPath);

        $this->appendHistoryPage($docxOutputPath, $attendance);

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

            $this->appendHistoryPage($docxFilePath, $attendance);

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
     * Append halaman ke-2 ke dalam file .docx yang sudah ada:
     * Background gambar MASTAMARU + tabel rekap histori presensi peserta.
     */
    private function appendHistoryPage(string $docxPath, Attendance $attendance): void
    {
        if (! file_exists($docxPath)) {
            return;
        }

        $submissions = AttendanceSubmission::where('student_id', $attendance->id)
            ->with('presenceSession')
            ->orderBy('submitted_at')
            ->get();

        if (! $attendance->relationLoaded('assessment')) {
            $attendance->load('assessment');
        }
        if (! $attendance->relationLoaded('group')) {
            $attendance->load('group');
        }
        if (! $attendance->relationLoaded('mentor')) {
            $attendance->load('mentor');
        }

        $assessment  = $attendance->assessment;
        $totalPoints = $assessment ? $assessment->total_presence_points : $submissions->sum('score_points');
        $grade       = $assessment ? strtoupper($assessment->grade) : 'D';
        $phpWord = new \PhpOffice\PhpWord\PhpWord;
        $section = $phpWord->addSection([
            'orientation'  => 'landscape',
            'marginTop'    => Converter::cmToTwip(1.5),
            'marginBottom' => Converter::cmToTwip(1.5),
            'marginLeft'   => Converter::cmToTwip(2),
            'marginRight'  => Converter::cmToTwip(2),
            'headerHeight' => Converter::cmToTwip(0),
        ]);

        $bgPath = public_path('img/background_history_points_attendance_on_certificate.png');
        if (file_exists($bgPath)) {
            $header = $section->addHeader();
            $header->addImage($bgPath, [
                'width'            => Converter::cmToPixel(29.7),
                'height'           => Converter::cmToPixel(21.0),
                'positioning'      => ImageStyle::POSITION_ABSOLUTE,
                'posHorizontal'    => ImageStyle::POSITION_HORIZONTAL_LEFT,
                'posVertical'      => ImageStyle::POSITION_VERTICAL_TOP,
                'posHorizontalRel' => 'page',
                'posVerticalRel'   => 'page',
                'wrappingStyle'    => ImageStyle::WRAPPING_STYLE_BEHIND,
            ]);
        }

        $titleFont  = ['bold' => true, 'size' => 14, 'name' => 'Times New Roman', 'color' => '6b0000'];
        $headerFont = ['bold' => true, 'size' => 10, 'name' => 'Times New Roman', 'color' => 'ffffff'];
        $bodyFont   = ['size' => 9, 'name' => 'Times New Roman'];
        $boldFont   = ['bold' => true, 'size' => 9, 'name' => 'Times New Roman'];
        $centerPara = ['alignment' => Jc::CENTER, 'spaceAfter' => 60];
        $leftPara   = ['alignment' => Jc::START, 'spaceAfter' => 0];

        $section->addText(
            'REKAP HISTORI KEHADIRAN — '.$attendance->name,
            $titleFont,
            $centerPara
        );
        $section->addText(
            'NIM: '.$attendance->student_id.' | Kelompok: '.($attendance->group->name ?? '-').' | Pemandu: '.($attendance->mentor->name ?? '-'),
            $bodyFont,
            $centerPara
        );

        $tableStyle = [
            'borderSize'  => 6,
            'borderColor' => 'cccccc',
            'cellMargin'  => 60,
        ];
        $table = $section->addTable($tableStyle);

        $headerBg = ['bgColor' => '6b0000', 'borderSize' => 6, 'borderColor' => '6b0000'];

        $colWidths = [
            'No'              => Converter::cmToTwip(0.8),
            'Hari'            => Converter::cmToTwip(1.5),
            'Tanggal & Waktu' => Converter::cmToTwip(4.5),
            'Nama Sesi'       => Converter::cmToTwip(8.5),
            'Tipe'            => Converter::cmToTwip(2.5),
            'Status'          => Converter::cmToTwip(2.5),
            'Poin'            => Converter::cmToTwip(2),
        ];

        $table->addRow(Converter::cmToTwip(0.7));
        foreach ($colWidths as $label => $width) {
            $cell = $table->addCell($width, $headerBg);
            $cell->addText($label, $headerFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        }

        $rowIndex = 1;
        foreach ($submissions as $sub) {
            $session  = $sub->presenceSession;
            $rowBg    = ($rowIndex % 2 === 0)
                ? ['bgColor' => 'fdf2f2', 'borderSize' => 4, 'borderColor' => 'e5e7eb']
                : ['bgColor' => 'ffffff', 'borderSize' => 4, 'borderColor' => 'e5e7eb'];

            $statusLabel = match ($sub->status) {
                'hadir'     => 'Hadir',
                'terlambat' => 'Terlambat',
                'sakit'     => 'Sakit',
                'izin'      => 'Izin',
                'alpha'     => 'Alpha',
                default     => ucfirst($sub->status),
            };
            $waktu = $sub->submitted_at
                ? $sub->submitted_at->locale('id')->isoFormat('D MMM YYYY, HH:mm')
                : '-';

            $table->addRow();
            $table->addCell($colWidths['No'], $rowBg)->addText((string) $rowIndex, $bodyFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $table->addCell($colWidths['Hari'], $rowBg)->addText('Hari '.($session ? $session->day_number : '-'), $bodyFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $table->addCell($colWidths['Tanggal & Waktu'], $rowBg)->addText($waktu, $bodyFont, $leftPara);
            $table->addCell($colWidths['Nama Sesi'], $rowBg)->addText($session ? $session->session_name : '-', $bodyFont, $leftPara);
            $table->addCell($colWidths['Tipe'], $rowBg)->addText($session ? strtoupper($session->session_type) : '-', $bodyFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $table->addCell($colWidths['Status'], $rowBg)->addText($statusLabel, $boldFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $table->addCell($colWidths['Poin'], $rowBg)->addText('+'.((int) $sub->score_points).'p', $boldFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);

            $rowIndex++;
        }

        $totalBg = ['bgColor' => '6b0000', 'borderSize' => 6, 'borderColor' => '6b0000'];
        $table->addRow(Converter::cmToTwip(0.7));
        $mergedCell = $table->addCell(
            array_sum(array_slice(array_values($colWidths), 0, 6)),
            array_merge($totalBg, ['gridSpan' => 6])
        );
        $mergedCell->addText('TOTAL POIN KEHADIRAN', $headerFont, ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);
        $table->addCell($colWidths['Poin'], $totalBg)
            ->addText($totalPoints.'p', $headerFont, ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);

        $section->addTextBreak(1);
        $summaryText  = "Total Poin: {$totalPoints} / 100   |   Predikat: {$grade}";
        $summaryText .= '   |   Status: '.strtoupper($attendance->status ?? 'PROSES');
        $section->addText($summaryText, [
            'bold'  => true,
            'size'  => 11,
            'name'  => 'Times New Roman',
            'color' => '6b0000',
        ], $centerPara);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($docxPath);
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
            'headerHeight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0),
        ]);

        $bgImagePath = public_path('img/background_history_points_attendance_on_certificate.png');
        if (file_exists($bgImagePath)) {
            $header = $section->addHeader();
            $pageWidthCm  = 29.7;
            $pageHeightCm = 21.0;
            $header->addImage($bgImagePath, [
                'width'          => \PhpOffice\PhpWord\Shared\Converter::cmToPixel($pageWidthCm),
                'height'         => \PhpOffice\PhpWord\Shared\Converter::cmToPixel($pageHeightCm),
                'positioning'    => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal'  => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
                'posVertical'    => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                'posHorizontalRel' => 'page',
                'posVerticalRel'   => 'page',
                'wrappingStyle'  => \PhpOffice\PhpWord\Style\Image::WRAPPING_STYLE_BEHIND,
            ]);
        }
        // ─────────────────────────────────────────────────────────────────────
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
