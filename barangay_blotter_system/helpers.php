<?php
// inc/helpers.php

function is_logged_in(){
    return !empty($_SESSION['user_id']);
}

function require_login(){
    if(!is_logged_in()){
        header('Location: pubindex.php');
        exit;
    }
}

function current_user($pdo){
    if(!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT u.*, r.name as role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function audit($pdo, $user_id, $action){
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, ip) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $ip]);
}
