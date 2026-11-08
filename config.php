<?php
declare(strict_types=1);
function env_value(string $key, mixed $default=null): mixed {
    static $env=null;
    if ($env===null) {
        $env=[];
        $path=__DIR__.'/../.env';
        if (is_file($path)) foreach(file($path,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
            $line=trim($line); if($line===''||str_starts_with($line,'#')||!str_contains($line,'='))continue;
            [$k,$v]=explode('=',$line,2); $env[trim($k)]=trim(trim($v), "\"'");
        }
    }
    $v=$_ENV[$key]??$_SERVER[$key]??getenv($key);
    return ($v===false||$v===null||$v==='')?($env[$key]??$default):$v;
}
