<?php
require_once __DIR__.'authcheck.php'; // sets $user
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Blotter Entry - Barangay Blotter</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="gov-light">
<?php include __DIR__.'pubpartop.php'; ?>

<main class="container">
  <h2>Blotter Entry / Incident Recording</h2>
  <p class="muted">Create a new blotter case. Fill in as much detail as possible.</p>

  <div class="card" style="max-width:900px">
    <form id="blotterForm">
      <label>Reported At</label>
      <input name="reported_at" type="datetime-local" value="<?=date('Y-m-d\TH:i')?>" required>

      <label>Incident Type</label>
      <select name="incident_type_id" id="incident_type_id" required>
        <option value="">Loading...</option>
      </select>

      <label>Location</label>
      <input name="location" required>

      <h4>Complainant</h4>
      <label>Full name</label><input name="complainant_name" required>
      <label>Address</label><input name="complainant_address">
      <label>Contact</label><input name="complainant_contact">
      <label>Age</label><input name="complainant_age" type="number" min="0">
      <label>Sex</label>
      <select name="complainant_sex">
        <option value="Other">Prefer not to say</option>
        <option value="M">Male</option><option value="F">Female</option>
      </select>

      <h4>Respondent</h4>
      <label>Full name</label><input name="respondent_name" required>
      <label>Address</label><input name="respondent_address">
      <label>Contact</label><input name="respondent_contact">
      <label>Age</label><input name="respondent_age" type="number" min="0">
      <label>Sex</label>
      <select name="respondent_sex">
        <option value="Other">Prefer not to say</option>
        <option value="M">Male</option><option value="F">Female</option>
      </select>

      <label>Incident Statement</label>
      <textarea name="incident_statement" rows="5" required></textarea>

      <label>Action Taken</label>
      <textarea name="action_taken" rows="3"></textarea>

      <label>Status</label>
      <select name="status">
        <option value="ongoing">Ongoing</option>
        <option value="settled">Settled</option>
        <option value="dismissed">Dismissed</option>
      </select>

      <label>Official In Charge</label>
      <select name="official_in_charge" id="officials_select">
        <option value="">(None)</option>
      </select>

      <div style="margin-top:12px">
        <button type="submit" class="btn">Create Blotter</button>
        <span id="resultMsg" class="muted" style="margin-left:12px"></span>
      </div>
    </form>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', ()=>{
  // load incident types and officials
  fetch('/api/incident_types.php?action=list').then(r=>r.json()).then(types=>{
    const sel = document.getElementById('incident_type_id');
    sel.innerHTML = '<option value=\"\">-- select --</option>';
    types.forEach(t => {
      const opt = document.createElement('option'); opt.value = t.id; opt.textContent = t.type_name;
      sel.appendChild(opt);
    });
  }).catch(()=>{ document.getElementById('incident_type_id').innerHTML = '<option value=\"\">Other</option>' });

  fetch('/api/users.php?action=list_officials').then(r=>r.json()).then(list=>{
    const sel = document.getElementById('officials_select');
    sel.innerHTML = '<option value=\"\">(None)</option>';
    list.forEach(u => {
      const opt = document.createElement('option'); opt.value = u.id; opt.textContent = u.fullname;
      sel.appendChild(opt);
    });
  }).catch(()=>{});

  const form = document.getElementById('blotterForm');
  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(form);
    fd.append('action','create');
    document.querySelector('.btn').disabled = true;
    const res = await fetch('apiblotter.php', {method:'POST', body:fd});
    const data = await res.json();
    document.querySelector('.btn').disabled = false;
    const msg = document.getElementById('resultMsg');
    if(data.success){
      msg.textContent = 'Created: ' + data.blotter_no;
      form.reset();
    } else {
      msg.textContent = data.message || 'Error creating blotter';
    }
  });
});
</script>
</body>
</html>
