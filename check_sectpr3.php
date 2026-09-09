<?php
$xml = file_get_contents('temp_cert_extract/word/document.xml');
preg_match_all('/<w:sectPr[^>]*>.*?<\/w:sectPr>/s', $xml, $matches);
foreach ($matches[0] as $i => $match) {
    echo "--- SectPr $i ---\n";
    echo $match . "\n";
}
