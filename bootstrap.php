<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
require_once __DIR__.'/database.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/auth.php';
$isHttps=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off');
session_name('diamondx_session');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
