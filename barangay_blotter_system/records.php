<?php
require_once __DIR__.'authcheck.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Records - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<?php include __DIR__.'pubpartop.php'; ?>

<main class="container">
  <h2>Records Management</h2>
  <p class="muted">Search, filter and view blotter cases.</p>

  <div class="card">
    <form id="filterForm" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <label style="margin:0">From</label>
      <input type="date" name="from">
      <label style="margin:0">To</label>
      <input type="date" name="to">
      <label style="margin:0">Type</label>
      <select name="incident_type_id" id="filter_type"><option value="">All</option></select>
      <label style="margin:0">Status</label>
      <select name="status"><option value="">All</option><option value="ongoing">Ongoing</option><option value="settled">Settled</option><option value="dismissed">Dismissed</option></select>
      <input name="q" placeholder="Search name or blotter no" style="min-width:200px">
      <button class="btn">Filter</button>
      <button id="resetBtn" type="button" style="background:transparent;border:1px solid #e6eef8;padding:8px;border-radius:6px">Reset</button>
    </form>
  </div>

  <div class="card" style="margin-top:12px">
    <table id="recordsTable" class="table">
      <thead>
        <tr><th>#</th><th>Blotter No</th><th>Type</th><th>Reported</th><th>Complainant</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', ()=>{
  // load types
  fetch('/api/incident_types.php?action=list').then(r=>r.json()).then(types=>{
    const sel = document.getElementById('filter_type');
    types.forEach(t=> sel.appendChild(new Option(t.type_name, t.id)));
  }).catch(()=>{});

  async function loadList(params = {}){
    // construct query for api/blotter.php?action=list with optional filters
    const qs = new URLSearchParams(params);
    qs.set('action','list');
    const res = await fetch('apiblotter.php?' + qs.toString());
    const data = await res.json();
    const tbody = document.querySelector('#recordsTable tbody');
    tbody.innerHTML = '';
    data.forEach((r,i)=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${i+1}</td>
        <td>${r.blotter_no}</td>
        <td>${r.type_name||''}</td>
        <td>${r.reported_at}</td>
        <td>${r.complainant||''}</td>
        <td>${r.status}</td>
        <td><a href="casemanagement.php?id=${r.id}">Open</a></td>`;
      tbody.appendChild(tr);
    });
  }

  document.getElementById('filterForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    const params = {};
    for(const [k,v] of fd.entries()){ if(v) params[k] = v; }
    loadList(params);
  });

  document.getElementById('resetBtn').addEventListener('click', ()=>{
    document.getElementById('filterForm').reset();
    loadList();
  });

  loadList();
});
</script>
</body>
</html>
