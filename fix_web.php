<?php
$c = file_get_contents('routes/web.php');
$c = str_replace([' Illuminate\Http', ' Spatie\Permission', ' App\Http'], [' \Illuminate\Http', ' \Spatie\Permission', ' \App\Http'], $c);
$c = str_replace(['[App\Http', '[Spatie\Permission', ' Illuminate\Support'], ['[\App\Http', '[\Spatie\Permission', ' \Illuminate\Support'], $c);
file_put_contents('routes/web.php', $c);
echo "Fixed slashes in web.php";
