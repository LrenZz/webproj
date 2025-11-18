<?php
// public/index.php
require_once __DIR__.'connect.php';
if(isset($_SESSION['user_id'])) header('Location: dashboard.php');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<div class="container auth">
  <div class="card auth-card">
    <h1>Barangay Blotter System</h1>
    <p class="muted">Official Barangay Case Recording System</p>
    <form id="loginForm">
      <label>Username</label>
      <input name="username" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit" class="btn">Sign in</button>
    </form>
    <div id="msg" class="muted"></div>
  </div>
</div>
<script src="main.js"></script>
</body>
</html>
