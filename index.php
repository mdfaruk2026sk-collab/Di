<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/bootstrap.php';
if(is_admin()){header('Location: dashboard.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();if(admin_login(trim((string)($_POST['username']??'')),(string)($_POST['password']??''))){header('Location: dashboard.php');exit;}$error='লগইন তথ্য সঠিক নয়।';}
?><!doctype html><html lang="bn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login | DIAMONDX BD</title><link rel="stylesheet" href="../public/assets/css/style.css"></head><body><main class="maintenance"><form method="post" class="search-panel" style="width:min(430px,92vw)"><h2>DIAMONDX BD Admin</h2><?php if($error):?><p style="color:#ff91a8"><?=e($error)?></p><?php endif;?><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label>Username</label><input name="username" required autocomplete="username"><label>Password</label><input type="password" name="password" required autocomplete="current-password"><button class="soft-btn" type="submit">Secure Login</button></form></main></body></html>
