<?php
declare(strict_types=1);
function e(mixed $v): string { return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function json_response(array $data,int $status=200): never { http_response_code($status); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE); exit; }
function setting(string $key,string $default=''): string {
    try{$s=db()->prepare('SELECT setting_value FROM settings WHERE setting_key=?');$s->execute([$key]);$v=$s->fetchColumn();return $v===false?$default:(string)$v;}catch(Throwable){return $default;}
}
function log_event(string $status,string $uid='',string $detail=''): void {
    try{$s=db()->prepare('INSERT INTO search_logs (uid,status,detail,ip_address,user_agent,created_at) VALUES (?,?,?,?,?,NOW())');$s->execute([$uid,$status,substr($detail,0,190),substr((string)($_SERVER['REMOTE_ADDR']??''),0,45),substr((string)($_SERVER['HTTP_USER_AGENT']??''),0,250)]);}catch(Throwable $e){error_log('[search-log] '.$e->getMessage());}
}
function csrf_token(): string { if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf']; }
function verify_csrf(): void { $t=(string)($_POST['csrf']??'');if(!hash_equals((string)($_SESSION['csrf']??''),$t)){http_response_code(419);exit('Session expired. Please go back and try again.');} }
