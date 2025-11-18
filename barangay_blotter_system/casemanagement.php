<?php
require_once __DIR__.'authcheck.php'; // sets $user
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Case Management - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<?php include __DIR__.'/partials/topnav.php'; ?>

<main class="container">
  <h2>Case Management</h2>
  <p class="muted">View case details, add updates and record actions.</p>

  <div class="card" id="caseCard" style="max-width:1100px">
    <div id="loading">Loading case...</div>

    <div id="caseDetails" style="display:none">
      <h3 id="blotter_no"></h3>
      <div style="display:flex;gap:18px;flex-wrap:wrap">
        <div style="flex:1;min-width:260px">
          <p><strong>Reported:</strong> <span id="reported_at"></span></p>
          <p><strong>Type:</strong> <span id="type_name"></span></p>
          <p><strong>Location:</strong> <span id="location"></span></p>
          <p><strong>Status:</strong> <span id="status_badge"></span></p>
        </div>
        <div style="flex:1;min-width:260px">
          <p><strong>Complainant:</strong> <span id="complainant_name"></span></p>
          <p><strong>Respondent:</strong> <span id="respondent_name"></span></p>
          <p><strong>Official:</strong> <span id="official"></span></p>
        </div>
      </div>

      <h4>Incident Statement</h4><p id="incident_statement"></p>
      <h4>Action Taken</h4><p id="action_taken"></p>

      <section style="margin-top:14px">
        <h4>Case Actions / Logs</h4>
        <div id="actionsList" class="muted">Loading...</div>

        <h5 style="margin-top:12px">Add Action</h5>
        <form id="addActionForm">
          <label>Action Date</label>
          <input name="action_date" type="datetime-local" value="<?=date('Y-m-d\TH:i')?>" required>
          <label>Note</label>
          <textarea name="note" rows="3" required></textarea>
          <div style="margin-top:8px">
            <button class="btn">Save Action</button>
            <span id="actionMsg" class="muted" style="margin-left:10px"></span>
          </div>
        </form>

        <h5 style="margin-top:12px">Change Status</h5>
        <form id="changeStatusForm" style="display:flex;gap:8px;align-items:center">
          <select name="status" id="status_select">
            <option value="ongoing">Ongoing</option>
            <option value="settled">Settled</option>
            <option value="dismissed">Dismissed</option>
          </select>
          <button id="statusBtn" class="btn">Update Status</button>
          <span id="statusMsg" class="muted"></span>
        </form>
      </section>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', ()=>{
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  if(!id){
    document.getElementById('loading').textContent = 'No case specified. Use Records or Dashboard to open a case.';
    return;
  }

  async function loadCase(){
    const res = await fetch('apiblotter.php?action=get&id=' + encodeURIComponent(id));
    if(!res.ok){ document.getElementById('loading').textContent = 'Failed to load case'; return; }
    const data = await res.json();
    if(!data){ document.getElementById('loading').textContent = 'Case not found'; return; }
    document.getElementById('loading').style.display = 'none';
    document.getElementById('caseDetails').style.display = 'block';

    document.getElementById('blotter_no').textContent = data.blotter_no;
    document.getElementById('reported_at').textContent = data.reported_at;
    document.getElementById('type_name').textContent = data.type_name || '—';
    document.getElementById('location').textContent = data.location || '—';
    document.getElementById('complainant_name').textContent = data.fullname || '—';
    document.getElementById('respondent_name').textContent = data.fullname || '—';
    document.getElementById('incident_statement').textContent = data.incident_statement || '—';
    document.getElementById('action_taken').textContent = data.action_taken || '—';
    document.getElementById('official').textContent = data.official || '—';
    document.getElementById('status_select').value = data.status || 'ongoing';
    document.getElementById('status_badge').textContent = (data.status || '—').toUpperCase();

    loadActions();
  }

  async function loadActions(){
    const res = await fetch('apiblotter.php?action=actions&blotter_id=' + encodeURIComponent(new URLSearchParams(window.location.search).get('id')));
    if(!res.ok){ document.getElementById('actionsList').textContent = 'No actions found'; return; }
    const list = await res.json();
    if(!list.length){ document.getElementById('actionsList').textContent = 'No actions recorded yet.'; return; }
    const wrap = document.createElement('div');
    list.forEach(a=>{
      const d = document.createElement('div');
      d.style.borderBottom = '1px solid #eef4fb'; d.style.padding = '8px 0';
      d.innerHTML = `<strong>${a.action_date}</strong> — ${a.note} <div class="muted" style="font-size:12px">By: ${a.actor_name || '—'}</div>`;
      wrap.appendChild(d);
    });
    const c = document.getElementById('actionsList');
    c.innerHTML = ''; c.appendChild(wrap);
  }

  // add action handler
  document.getElementById('addActionForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action','add_action');
    fd.append('blotter_id', new URLSearchParams(window.location.search).get('id'));
    const btn = e.target.querySelector('button'); btn.disabled = true;
    const res = await fetch('apiblotter.php', {method:'POST', body:fd});
    btn.disabled = false;
    const data = await res.json();
    const msg = document.getElementById('actionMsg');
    if(data.success){ msg.textContent = 'Saved'; e.target.reset(); loadActions(); }
    else msg.textContent = data.message || 'Error';
  });

  // change status
  document.getElementById('changeStatusForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const status = document.getElementById('status_select').value;
    const fd = new FormData();
    fd.append('action','update_status');
    fd.append('blotter_id', new URLSearchParams(window.location.search).get('id'));
    fd.append('status', status);
    document.getElementById('statusBtn').disabled = true;
    const res = await fetch('apiblotter.php', {method:'POST', body:fd});
    document.getElementById('statusBtn').disabled = false;
    const data = await res.json();
    const msg = document.getElementById('statusMsg');
    if(data.success){ msg.textContent = 'Updated'; loadCase(); } else msg.textContent = data.message || 'Error';
  });

  loadCase();
});
</script>
</body>
</html>
