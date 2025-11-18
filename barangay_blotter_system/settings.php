<?php
require_once __DIR__.'authcheck.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Settings - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<?php include __DIR__.'/partials/topnav.php'; ?>

<main class="container">
  <h2>Settings & Configuration</h2>
  <p class="muted">Manage incident types, barangay info and system preferences.</p>

  <div class="card" style="max-width:900px">
    <h3>Incident Types</h3>
    <div id="typesList" class="muted">Loading...</div>

    <form id="addTypeForm" style="margin-top:8px;display:flex;gap:8px;align-items:center">
      <input name="type_name" placeholder="New incident type (e.g. Trespassing)" required>
      <button class="btn">Add Type</button>
    </form>
  </div>

  <div class="card" style="margin-top:12px">
    <h3>Officials</h3>
    <div id="officialsList" class="muted">Loading...</div>
    <p class="muted" style="margin-top:8px">To add or edit officials, use the admin panel or database. (You can extend this UI to add CRUD for users.)</p>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', ()=>{
  async function loadTypes(){
    const res = await fetch('/api/incident_types.php?action=list');
    const types = await res.json();
    const wrap = document.getElementById('typesList');
    wrap.innerHTML = '';
    types.forEach(t=>{
      const el = document.createElement('div');
      el.style.display = 'flex'; el.style.justifyContent = 'space-between'; el.style.padding = '8px 0';
      el.innerHTML = `<div>${t.type_name}</div><div><button data-id="${t.id}" class="delType" style="background:transparent;border:0;color:#c53030;cursor:pointer">Delete</button></div>`;
      wrap.appendChild(el);
    });
  }

  async function loadOfficials(){
    const res = await fetch('apiusers.php?action=list_officials');
    const list = await res.json();
    const wrap = document.getElementById('officialsList');
    wrap.innerHTML = '';
    list.forEach(u=>{
      const el = document.createElement('div');
      el.style.padding = '6px 0';
      el.innerHTML = `<strong>${u.fullname}</strong> — <span class="muted">${u.username}</span> (<em>${u.role}</em>)`;
      wrap.appendChild(el);
    });
  }

  document.getElementById('addTypeForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action','create');
    const res = await fetch('/api/incident_types.php', {method:'POST', body:fd});
    const data = await res.json();
    if(data.success){ loadTypes(); e.target.reset(); } else alert(data.message || 'Error');
  });

  document.addEventListener('click', async (e)=>{
    if(e.target.matches('.delType')){
      const id = e.target.dataset.id;
      if(!confirm('Delete this incident type?')) return;
      const fd = new FormData(); fd.append('action','delete'); fd.append('id', id);
      const res = await fetch('/api/incident_types.php', {method:'POST', body:fd});
      const data = await res.json();
      if(data.success) loadTypes(); else alert(data.message || 'Error');
    }
  });

  loadTypes(); loadOfficials();
});
</script>
</body>
</html>
