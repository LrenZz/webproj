<?php
// POST { action: 'login', username, password } OR GET action=logout
require_once __DIR__ . 'connect.php';
require_once __DIR__ . 'helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'POST'){
    $action = $_POST['action'] ?? '';
    if($action === 'login'){
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            audit($pdo, $user['id'], 'login');
            echo json_encode(['success'=>true]);
            exit;
        } else {
            echo json_encode(['success'=>false,'message'=>'Invalid credentials']);
            exit;
        }
    }
}

if($method === 'GET' && ($_GET['action'] ?? '') === 'logout'){
    if(isset($_SESSION['user_id'])) {
        audit($pdo, $_SESSION['user_id'], 'logout');
    }
    session_destroy();
    header('Location: pubindex.php');
    exit;
}
