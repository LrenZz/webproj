<?php
require_once __DIR__.'authcheck.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reports - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<?php include __DIR__.'pubpartop.php'; ?>

<main class="container">
  <h2>Reports & Export</h2>
  <p class="muted">Generate printable reports or export CSV for the selected period and filters.</p>

  <div class="card" style="max-width:900px">
    <form id="reportForm" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <label>From</label><input type="date" name="from" required>
      <label>To</label><input type="date" name="to" required>
      <label>Type</label>
      <select name="incident_type_id" id="report_type"><option value="">All</option></select>
      <label>Status</label>
      <select name="status"><option value="">All</option><option value="ongoing">Ongoing</option><option value="settled">Settled</option><option value="dismissed">Dismissed</option></select>

      <div style="width:100%;margin-top:12px">
        <button class="btn" id="exportCsv">Export CSV</button>
        <button type="button" class="btn" id="printReport" style="background:var(--accent-2);margin-left:8px">Generate Printable</button>
        <span id="reportMsg" class="muted" style="margin-left:12px"></span>
      </div>
    </form>
  </div>

  <div id="printArea" class="card" style="margin-top:12px;display:none">
    <h3>Report</h3>
    <div id="reportContent" class="muted">Printable preview will appear here.</div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', ()=>{
  // load types
  fetch('/api/incident_types.php?action=list').then(r=>r.json()).then(types=>{
    const sel = document.getElementById('report_type');
    types.forEach(t=> sel.appendChild(new Option(t.type_name, t.id)));
  }).catch(()=>{});

  document.getElementById('exportCsv').addEventListener('click', (e)=>{
    e.preventDefault();
    const fd = new FormData(document.getElementById('reportForm'));
    const params = new URLSearchParams();
    for(const [k,v] of fd.entries()) if(v) params.append(k,v);
    // navigate to CSV export endpoint (will prompt download)
    window.location.href = 'reports.php?action=export_csv&' + params.toString();
  });

  document.getElementById('printReport').addEventListener('click', async ()=>{
    const fd = new FormData(document.getElementById('reportForm'));
    const params = {};
    for(const [k,v] of fd.entries()) if(v) params[k] = v;
    const res = await fetch('reports.php?action=preview', {
      method: 'POST',
      headers: { 'Accept':'application/json' },
      body: new URLSearchParams(params)
    });
    const data = await res.json();
    // data.html contains printable HTML
    document.getElementById('printArea').style.display = 'block';
    document.getElementById('reportContent').innerHTML = data.html || '<p>No results</p>';
    // open print dialog
    window.print();
  });
});
</script>
</body>
</html>
