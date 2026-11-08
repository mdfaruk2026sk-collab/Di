<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/database.php';
if(PHP_SAPI!=='cli'){http_response_code(403);exit("Run this script from the server terminal only.\n");}
$username=$argv[1]??'';$password=$argv[2]??'';
if(!preg_match('/^[a-zA-Z0-9_.-]{3,40}$/',$username)||strlen($password)<12){fwrite(STDERR,"Usage: php scripts/create_admin.php USERNAME STRONG_PASSWORD (12+ chars)\n");exit(1);}
$hash=password_hash($password,PASSWORD_DEFAULT);
$s=db()->prepare('INSERT INTO admins(username,password_hash,created_at) VALUES(?,?,NOW()) ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash)');
$s->execute([$username,$hash]);echo "Admin account created/updated.\n";
