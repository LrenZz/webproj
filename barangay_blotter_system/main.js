// main.js
document.addEventListener('DOMContentLoaded', ()=>{
  const loginForm = document.getElementById('loginForm');
  if(loginForm){
    loginForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd = new FormData(loginForm);
      fd.append('action','login');
      const res = await fetch('/api/auth.php', {method:'POST', body:fd});
      const data = await res.json();
      const msg = document.getElementById('msg');
      if(data.success){
        window.location.href = 'dashboard.php';
      } else {
        msg.innerText = data.message || 'Invalid credentials';
      }
    });
  }
});
