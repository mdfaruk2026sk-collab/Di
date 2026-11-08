<?php
declare(strict_types=1);
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if(!is_admin()){header('Location: index.php');exit;} }
function admin_login(string $username,string $password): bool {
    $s=db()->prepare('SELECT id,password_hash FROM admins WHERE username=? LIMIT 1');$s->execute([$username]);$row=$s->fetch();
    if(!$row||!password_verify($password,$row['password_hash']))return false;
    session_regenerate_id(true);$_SESSION['admin_id']=(int)$row['id'];$_SESSION['admin_user']=$username;
    db()->prepare('UPDATE admins SET last_login=NOW() WHERE id=?')->execute([(int)$row['id']]);return true;
}
