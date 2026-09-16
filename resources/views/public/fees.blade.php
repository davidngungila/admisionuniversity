@extends('layouts.app')
@section('title','Fees')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <h1>Application Fees</h1>
            <p class="page-sub">Fees are configured per admission window (level + round + applicant category).</p>
        </div>
        <div class="page-actions">
            <span class="tag tag-grey">{{ $windows->count() }} windows</span>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg></div><span class="tag tag-green">Free</span></div>
            <div class="stat-label">Free Windows</div>
            <div class="stat-value">{{ $windows->where('application_fee','<=',0)->count() }}</div>
            <div class="stat-sub">No fee required</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div><span class="tag tag-gold">Paid</span></div>
            <div class="stat-label">Paid Windows</div>
            <div class="stat-value">{{ $windows->where('application_fee','>',0)->count() }}</div>
            <div class="stat-sub">Fee required</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg></div><span class="tag tag-blue">Open</span></div>
            <div class="stat-label">Open</div>
            <div class="stat-value">{{ $windows->filter(fn($w)=>$w->statusLabel()==='OPEN')->count() }}</div>
            <div class="stat-sub">Currently accepting</div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <div class="chip-filters">
                <span class="muted" style="font-size:12.5px;font-weight:600;">Fee schedule</span>
                <span class="tag tag-green">Free</span>
                <span class="tag tag-gold">Paid</span>
            </div>
            <div class="table-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-3.5-3.5"/></svg>
                <input id="tableSearch" placeholder="Search year, level, category…">
            </div>
        </div>
        <div class="table-scroll">
            <table id="feesTable">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Level</th>
                        <th class="center">Round</th>
                        <th>Category</th>
                        <th class="right">Fee</th>
                        <th class="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($windows as $w)
                        <tr>
                            <td><span class="cell-title">{{ $w->academicYear->name }}</span></td>
                            <td>
                                <div class="cell-main">
                                    <div class="thumb thumb-coffee">{{ strtoupper(substr($w->admissionLevel->name,0,2)) }}</div>
                                    <div class="cell-title">{{ $w->admissionLevel->name }}</div>
                                </div>
                            </td>
                            <td class="center"><span class="tag tag-coffee">{{ $w->applicationRound->round_number }}</span></td>
                            <td>{{ $w->applicant_category }}</td>
                            <td class="right"><span style="font-weight:800;{{ $w->application_fee <= 0 ? 'color:var(--acacia-600);' : 'color:var(--coffee-900);' }}">{{ $w->feeLabel() }}</span></td>
                            <td class="center">
                                @if($w->statusLabel()==='OPEN')
                                    <span class="tag tag-green">OPEN</span>
                                @elseif($w->statusLabel()==='CLOSING SOON')
                                    <span class="tag tag-gold">CLOSING SOON</span>
                                @elseif($w->statusLabel()==='UPCOMING')
                                    <span class="tag tag-blue">UPCOMING</span>
                                @else
                                    <span class="tag tag-grey">{{ $w->statusLabel() }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div><strong>No fee data</strong><p>No windows configured.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const inp=document.getElementById('tableSearch');
  const tbl=document.getElementById('feesTable');
  if(!inp||!tbl) return;
  inp.addEventListener('input',()=>{
    const q=inp.value.toLowerCase();
    tbl.querySelectorAll('tbody tr').forEach(tr=>{
      if(tr.querySelector('.empty-state')) return;
      tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';
    });
  });
});
</script>
@endsection
