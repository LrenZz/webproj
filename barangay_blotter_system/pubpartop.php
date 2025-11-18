<?php
// public/partials/topnav.php
if(!isset($user)){
    require_once __DIR__.'authcheck.php';
    $user = current_user($pdo);
}
?>
<header class="topnav">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="dashboard.php"><strong>Barangay Blotter</strong></a>
    <nav aria-label="main nav">
      <a href="dashboard.php">Dashboard</a>
      <a href="blotter_entry.php">Blotter Entry</a>
      <a href="case_management.php">Case Management</a>
      <a href="records.php">Records</a>
      <a href="reports.php">Reports</a>
      <a href="settings.php">Settings</a>
    </nav>
  </div>
  <div style="display:flex;align-items:center;gap:12px">
    <div style="text-align:right">
      <div style="font-weight:600"><?=htmlspecialchars($user['fullname'])?></div>
      <div class="muted" style="font-size:12px"><?=htmlspecialchars($user['role'])?></div>
    </div>
    <a href="api.php?action=logout" class="btn" style="background:transparent;color:var(--accent);border:1px solid #e6eef8;padding:8px;border-radius:6px;text-decoration:none">Logout</a>
  </div>
</header>
