@extends('layouts.app')
@section('title','Application Calendar & Deadlines')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <h1>Application Calendar &amp; Deadlines</h1>
            <p class="page-sub">All windows are calculated automatically (OPEN / CLOSING SOON / CLOSED / UPCOMING) from configured dates. Timezone: Africa/Dar_es_Salaam.</p>
        </div>
        <div class="page-actions">
            <span class="tag tag-grey">Timezone: Africa/Dar_es_Salaam</span>
        </div>
    </div>

    {{-- Table-card view for larger screens / accessible list for all --}}
    <div class="table-card" style="margin-bottom:22px;">
        <div class="table-toolbar">
            <div class="chip-filters">
                <span class="tag tag-green">OPEN</span>
                <span class="tag tag-gold">CLOSING SOON</span>
                <span class="tag tag-blue">UPCOMING</span>
                <span class="tag tag-grey">CLOSED</span>
            </div>
            <div class="table-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-3.5-3.5"/></svg>
                <input id="tableSearch" placeholder="Search level, round, category…">
            </div>
        </div>
        <div class="table-scroll">
            <table id="calendarTable">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Round / Year</th>
                        <th>Opening</th>
                        <th>Closing</th>
                        <th>Category</th>
                        <th class="right">Fee</th>
                        <th class="center">Status</th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($windows as $w)
                        <tr>
                            <td>
                                <div class="cell-main">
                                    <div class="thumb thumb-coffee">{{ strtoupper(substr($w->admissionLevel->name,0,2)) }}</div>
                                    <div>
                                        <div class="cell-title">{{ $w->admissionLevel->name }}</div>
                                        <div class="cell-sub">{{ $w->timezone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="cell-title">Round {{ $w->applicationRound->round_number }}</span><div class="cell-sub">{{ $w->academicYear->name }}</div></td>
                            <td><span class="cell-mono">{{ $w->opens_at->format('d M Y H:i') }}</span></td>
                            <td><span class="cell-mono">{{ $w->closes_at->format('d M Y H:i') }}</span></td>
                            <td>{{ $w->applicant_category }}</td>
                            <td class="right"><span style="font-weight:800;{{ $w->application_fee <= 0 ? 'color:var(--acacia-600);' : '' }}">{{ $w->feeLabel() }}</span></td>
                            <td class="center">
                                @if($w->statusLabel()==='OPEN')
                                    <span class="tag tag-green">OPEN</span>
                                @elseif($w->statusLabel()==='CLOSING SOON')
                                    <span class="tag tag-gold">CLOSING SOON</span>
                                @elseif($w->statusLabel()==='UPCOMING')
                                    <span class="tag tag-blue">UPCOMING</span>
                                @else
                                    <span class="tag tag-grey">CLOSED</span>
                                @endif
                            </td>
                            <td class="right">
                                <div class="row-actions" style="flex-wrap:nowrap;white-space:nowrap;">
                                @if($w->isClosed())
                                    <span class="tag tag-red" style="white-space:nowrap;">Closed</span>
                                @elseif($w->isOpen())
                                    @auth
                                        @if(!auth()->user()->isAdmin())
                                            @if(in_array($w->id, $myApplicationWindowIds ?? [], true))
                                                <span class="tag tag-green" style="white-space:nowrap;">Already Applied</span>
                                            @elseif(in_array($w->academic_year_id, $myApplicationYearIds ?? [], true))
                                                <button type="button" disabled title="You already have an active application in {{ $w->academicYear->name }}. Only one application per academic year is allowed." style="white-space:nowrap;background:var(--sand-200);color:var(--ink-soft);border:none;padding:8px 14px;border-radius:8px;font-weight:600;font-size:13px;cursor:not-allowed;opacity:.7;">Applied {{ $w->academicYear->name }} — Apply Closed</button>
                                            @else
                                                <form method="POST" action="{{ route('applicant.application.start', encId($w->id)) }}" style="display:inline-flex;margin:0;" onsubmit="event.preventDefault(); const _f=this; confirmModal('Start application','Are you sure you want to start a new application for this window?',()=>_f.submit())">@csrf<button class="btn btn-primary btn-sm" style="white-space:nowrap;">Apply Now</button></form>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">Apply Now</a>
                                    @endauth
                                @else
                                    <span class="muted" style="font-size:12px;white-space:nowrap;">Opens {{ $w->opens_at->diffForHumans() }}</span>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">
                            <div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><strong>No admission windows configured yet.</strong><p>Please check back later.</p></div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const inp=document.getElementById('tableSearch');
  const tbl=document.getElementById('calendarTable');
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
