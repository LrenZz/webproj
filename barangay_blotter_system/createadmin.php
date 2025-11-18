<?php
require_once __DIR__.'connect.php';
$username = 'admin';
$password = 'admin123'; // CHANGE this after first login
$fullname = 'Barangay Administrator';
$role = 'admin';

$stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
$stmt->execute([$role]);
$r = $stmt->fetch();
if(!$r) { echo "Role not found\n"; exit; }
$role_id = $r['id'];

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT IGNORE INTO users (username,password,fullname,role_id) VALUES (?,?,?,?)");
$stmt->execute([$username, $hash, $fullname, $role_id]);

echo "Admin user created (username: $username, password: $password). Change password after first login.";
