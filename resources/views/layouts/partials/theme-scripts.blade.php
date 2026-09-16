{{-- Internal CMS JS: toast, modal, confirm, sidebar, idle logout --}}
<script>
function toast(msg, type='info'){
  const host=document.getElementById('toastHost');
  if(!host) return;
  const icons={success:'✓', error:'✕', info:'ℹ', warning:'⚠'};
  const el=document.createElement('div');
  el.className='toast '+(type||'info');
  el.style.display='flex'; el.style.alignItems='center'; el.style.gap='10px';
  const ic=document.createElement('span');
  ic.textContent=icons[type]||icons.info;
  ic.style.width='22px'; ic.style.height='22px'; ic.style.borderRadius='50%'; ic.style.display='inline-flex'; ic.style.alignItems='center'; ic.style.justifyContent='center'; ic.style.fontWeight='800'; ic.style.fontSize='12px'; ic.style.flex='none';
  if(type==='success'){ ic.style.background='var(--acacia-600)'; ic.style.color='#fff'; }
  else if(type==='error'){ ic.style.background='var(--danger)'; ic.style.color='#fff'; }
  else if(type==='warning'){ ic.style.background='var(--gold-500)'; ic.style.color='#fff'; }
  else { ic.style.background='var(--coffee-700)'; ic.style.color='#fff'; }
  const tx=document.createElement('span'); tx.textContent=msg; tx.style.flex='1';
  const close=document.createElement('button'); close.textContent='×'; close.style.background='transparent'; close.style.border='none'; close.style.color='rgba(255,255,255,.7)'; close.style.fontSize='18px'; close.style.cursor='pointer'; close.style.lineHeight='1'; close.onclick=()=>{ el.classList.add('out'); setTimeout(()=>el.remove(),300); };
  el.appendChild(ic); el.appendChild(tx); el.appendChild(close);
  host.appendChild(el);
  // progress bar
  const bar=document.createElement('div'); bar.style.position='absolute'; bar.style.left='0'; bar.style.bottom='0'; bar.style.height='3px'; bar.style.background='rgba(255,255,255,.85)'; bar.style.width='100%'; bar.style.borderRadius='0 0 12px 12px'; bar.style.transition='width 3.6s linear';
  el.style.position='relative'; el.style.overflow='hidden'; el.appendChild(bar);
  requestAnimationFrame(()=>{ bar.style.width='0%'; });
  setTimeout(()=>{ el.classList.add('out'); setTimeout(()=>el.remove(),300); }, 3600);
}
function openModal(id){
  const el=document.getElementById(id);
  if(!el) return;
  el.classList.add('show');
  document.body.style.overflow='hidden';
}
function closeModal(id){
  const el=document.getElementById(id);
  if(!el) return;
  el.classList.remove('show');
  if(!document.querySelector('.modal-backdrop.show')) document.body.style.overflow='';
}
function confirmModal(title, msg, onConfirm){
  const t=document.getElementById('confirmTitle');
  const m=document.getElementById('confirmMsg');
  const b=document.getElementById('confirmBtn');
  if(t) t.textContent=title||'Are you sure?';
  if(m) m.textContent=msg||'This action cannot be undone.';
  if(b) b.onclick=function(){ closeConfirmModal(); if(typeof onConfirm==='function') onConfirm(); };
  openModal('confirmBackdrop');
}
function closeConfirmModal(){ closeModal('confirmBackdrop'); }
function toggleSidebar(){
  if(window.innerWidth <= 900){
    document.getElementById('sidebar')?.classList.toggle('mobile-open');
    document.getElementById('mobileOverlay')?.classList.toggle('show');
  } else {
    document.getElementById('sidebar')?.classList.toggle('collapsed');
  }
}
function closeMobile(){
  document.getElementById('sidebar')?.classList.remove('mobile-open');
  document.getElementById('mobileOverlay')?.classList.remove('show');
}
(function(){
  const idleMax=10*60*1000; let t;
  function reset(){ clearTimeout(t); t=setTimeout(()=>{ document.getElementById('idleLogoutForm')?.submit(); }, idleMax); }
  ['click','keydown','mousemove','touchstart','scroll'].forEach(ev=>window.addEventListener(ev, reset, {passive:true}));
  reset();
})();
document.addEventListener('DOMContentLoaded', function(){
  const s=document.getElementById('tableSearch');
  if(s){
    s.addEventListener('input', function(){
      const q=this.value.toLowerCase().trim();
      document.querySelectorAll('.table-scroll tbody tr').forEach(tr=>{
        if(!q){ tr.style.display=''; return; }
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }
  // chip filter: data-filter on rows via data-status / data-cat etc — generic: chip has data-filter, row has data-filter-value
  document.querySelectorAll('.chip[data-filter]').forEach(chip=>{
    chip.addEventListener('click', function(){
      document.querySelectorAll('.chip[data-filter]').forEach(c=>c.classList.remove('active'));
      this.classList.add('active');
      const f=this.getAttribute('data-filter');
      document.querySelectorAll('.table-scroll tbody tr').forEach(tr=>{
        if(f==='all'){ tr.style.display=''; return; }
        const v=(tr.getAttribute('data-filter')||'').toLowerCase();
        tr.style.display = (v===f || v.includes(f)) ? '' : 'none';
      });
      const si=document.getElementById('tableSearch');
      if(si) si.value='';
    });
  });
  // Sidebar scroll persistence
  const sbNav = document.querySelector('.sb-nav');
  if(sbNav){
    const saved = sessionStorage.getItem('sbScroll');
    if(saved) sbNav.scrollTop = parseInt(saved, 10);
    let sbTimer;
    sbNav.addEventListener('scroll', ()=>{
      clearTimeout(sbTimer);
      sbTimer = setTimeout(()=> sessionStorage.setItem('sbScroll', sbNav.scrollTop), 60);
    });
  }
  // Back button → logout (block navigating back into previous screens)
  const backGuardForm = document.getElementById('idleLogoutForm');
  if(backGuardForm){
    history.pushState({backGuard:true}, '', location.href);
    window.addEventListener('popstate', function(){
      backGuardForm.submit();
    });
  }
});
</script>
