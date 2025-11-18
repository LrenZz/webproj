<?php
require_once __DIR__.'connect.php';
require_once __DIR__.'helpers.php';
require_login();
$user = current_user($pdo);
