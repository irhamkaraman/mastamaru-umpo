<?php

$dirs = [
    __DIR__.'/app',
    __DIR__.'/database/migrations',
    __DIR__.'/routes',
];

function processDir($dir)
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            cleanFile($file->getPathname());
        }
    }
}

function cleanFile($path)
{
    $content = file_get_contents($path);
    $tokens = token_get_all($content);
    $newContent = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            // T_COMMENT is for // and /* */ (inline/block comments)
            // T_DOC_COMMENT is for /** */ (DocBlocks)
            // We keep T_DOC_COMMENT for IDE type hinting
            if ($token[0] === T_COMMENT) {
                continue; // Skip this token
            }
            $newContent .= $token[1];
        } else {
            $newContent .= $token;
        }
    }

    // Remove empty lines that might have been left by comments
    $newContent = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $newContent);

    file_put_contents($path, $newContent);
}

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        processDir($dir);
    }
}
echo "Comments removed successfully.\n";
