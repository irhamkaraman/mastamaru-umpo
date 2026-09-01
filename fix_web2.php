<?php
$content = file_get_contents('routes/web.php');
// Add leading slash to Illuminate, App, Spatie if not already there, not in a "use" statement, and not part of another word
$content = preg_replace('/(?<!\\\\)(?<!use\s)(?<!\w)(Illuminate|App|Spatie)\b/', '\\\$1', $content);
file_put_contents('routes/web.php', $content);
echo "Regex replaced.\n";
