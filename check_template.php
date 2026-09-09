<?php
$zip = new ZipArchive();
if ($zip->open('public/storage/certificate_templates/01M1DSR0HNJF5T07V92D6Y714E.docx')) {
    echo "Template opened successfully.\n";
    echo $zip->getFromName('word/document.xml');
} else {
    echo "Template not found or corrupt.\n";
}
