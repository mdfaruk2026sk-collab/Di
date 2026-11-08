<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_response(['success'=>false,'error'=>'method_not_allowed'],405);
$uid = trim((string)($_GET['uid'] ?? ''));
if (!preg_match('/^[0-9]{5,20}$/', $uid)) json_response(['success'=>false,'error'=>'invalid_uid'],400);
try {
    $db = db();
    $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'),0,45);
    $stmt = $db->prepare('INSERT INTO rate_limits (ip_address, window_start, hits) VALUES (?, NOW(), 1) ON DUPLICATE KEY UPDATE hits = IF(window_start < DATE_SUB(NOW(), INTERVAL 1 MINUTE), 1, hits + 1), window_start = IF(window_start < DATE_SUB(NOW(), INTERVAL 1 MINUTE), NOW(), window_start)');
    $stmt->execute([$ip]);
    $check = $db->prepare('SELECT hits FROM rate_limits WHERE ip_address=?'); $check->execute([$ip]);
    if ((int)$check->fetchColumn() > 25) json_response(['success'=>false,'error'=>'rate_limited'],429);
} catch (Throwable $e) { error_log('[rate-limit] '.$e->getMessage()); }
$endpoint = rtrim((string)env_value('PLAYER_API_URL', setting('player_api_url','https://auraffxserverinfocheckerapi.onrender.com/api/player')),'?');
$url = $endpoint . '?uid=' . rawurlencode($uid);
$body = ''; $http = 0; $curlError = '';
if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_CONNECTTIMEOUT=>7,CURLOPT_TIMEOUT=>15,CURLOPT_HTTPHEADER=>['Accept: application/json','User-Agent: DIAMONDX-BD/1.0']]);
    $body = (string)curl_exec($ch); $curlError = (string)curl_error($ch); $http = (int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
} else {
    $ctx = stream_context_create(['http'=>['timeout'=>15,'ignore_errors'=>true,'header'=>"Accept: application/json\r\nUser-Agent: DIAMONDX-BD/1.0\r\n"]]);
    $body = (string)@file_get_contents($url,false,$ctx);
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/',$http_response_header[0],$m)) $http=(int)$m[1];
}
if ($body === '') { log_event('api_error',$uid,'upstream_empty'); json_response(['success'=>false,'error'=>'api_unavailable'],502); }
$data = json_decode($body,true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) { log_event('api_error',$uid,'invalid_json'); json_response(['success'=>false,'error'=>'invalid_json'],502); }
if ($http >= 400) { log_event('failed',$uid,'upstream_http_'.$http); json_response(['success'=>false,'error'=>'api_error'],502); }
if (isset($data['error']) && $data['error']) { log_event('failed',$uid,'player_not_found'); json_response(['success'=>false,'error'=>'player_not_found'],404); }
if (empty($data)) { log_event('failed',$uid,'empty_response'); json_response(['success'=>false,'error'=>'player_not_found'],404); }
log_event('success',$uid,'');
json_response(['success'=>true,'data'=>$data]);
