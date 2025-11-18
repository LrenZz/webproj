<?php
require_once __DIR__.'authcheck.php'; // will set $user
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<?php include __DIR__.'pubpartop.php'; ?>
<main class="container">
  <h2>Welcome, <?=htmlspecialchars($user['fullname'])?></h2>
  <!-- Summary cards -->
  <div class="grid">
    <div class="card">
      <h3>Total Cases</h3>
      <div id="totalCases">—</div>
    </div>
    <div class="card">
      <h3>Ongoing</h3>
      <div id="ongoingCases">—</div>
    </div>
    <div class="card">
      <h3>Settled</h3>
      <div id="settledCases">—</div>
    </div>
  </div>

  <section>
    <h3>Recent Cases</h3>
    <table id="casesTable" class="table">
      <thead><tr><th>No</th><th>Blotter No</th><th>Type</th><th>Reported</th><th>Status</th><th>Action</th></tr></thead>
      <tbody></tbody>
    </table>
  </section>
</main>

<script>
  // load cases list
  fetch('apiblotter.php?action=list').then(r=>r.json()).then(data=>{
    document.getElementById('totalCases').innerText = data.length;
    document.getElementById('ongoingCases').innerText = data.filter(d=>d.status==='ongoing').length;
    document.getElementById('settledCases').innerText = data.filter(d=>d.status==='settled').length;
    const tbody = document.querySelector('#casesTable tbody');
    data.slice(0,10).forEach((row,i)=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${i+1}</td><td>${row.blotter_no}</td><td>${row.type_name||''}</td><td>${row.reported_at}</td><td>${row.status}</td><td><a href="case_management.php?id=${row.id}">View</a></td>`;
      tbody.appendChild(tr);
    });
  });
</script>
</body>
</html>
