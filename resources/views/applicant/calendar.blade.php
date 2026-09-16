@extends('layouts.applicant')
@section('title','My Calendar')
@section('content')
<div class="page-head">
    <div>
        <h1>My Admission Calendar</h1>
        <p class="page-sub">Your personalized deadlines — distinct from public calendar. Green = yours, compact timeline.</p>
    </div>
    <div class="page-actions">
        <span class="tag tag-grey">{{ $windows->count() }} windows</span>
        <a href="{{ route('public.calendar') }}" class="btn btn-ghost btn-sm">Public Calendar</a>
    </div>
</div>

<div class="panel" style="overflow:hidden;">
    <div class="panel-head" style="background:var(--sand-100)">
        <div class="panel-title" style="display:flex;align-items:center;gap:8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Timeline — All windows, yours highlighted</div>
        <span class="tag tag-green">You: {{ count($myAppWindowIds) }} applied</span>
    </div>
    <div class="panel-body" style="padding:0;">
        <div style="position:relative;padding:18px 18px 18px 36px;">
            <div style="position:absolute;left:22px;top:18px;bottom:18px;width:2px;background:var(--line);border-radius:2px;"></div>
            @forelse($windows as $w)
                @php $isMine = in_array($w->id, $myAppWindowIds); $status=$w->statusLabel(); $dotColor = $status==='OPEN' ? 'var(--acacia-600)' : ($status==='CLOSING SOON' ? 'var(--gold-500)' : ($status==='CLOSED' ? 'var(--danger)' : 'var(--terracotta-600)')); @endphp
                <div style="position:relative;display:flex;gap:14px;align-items:flex-start;margin-bottom:14px;">
                    <div style="position:absolute;left:-14px;top:14px;width:12px;height:12px;border-radius:50%;background:{{ $dotColor }};border:2px solid #fff;box-shadow:0 0 0 2px {{ $dotColor }};flex:none;"></div>
                    <div class="panel" style="flex:1;margin:0;@if($isMine)border:1.5px solid var(--acacia-600);box-shadow:0 4px 16px rgba(94,110,63,.12);@endif">
                        <div class="panel-body" style="padding:14px 16px;display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">
                                <div class="thumb {{ $isMine ? 'thumb-green' : 'thumb-grey' }}" style="width:42px;height:42px;">{{ strtoupper(substr($w->admissionLevel->short_name,0,1)) }}</div>
                                <div style="min-width:0;flex:1;">
                                    <div style="font-weight:800;color:var(--coffee-900);font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $w->admissionLevel->name }} · Round {{ $w->applicationRound->round_number }} @if($isMine)<span class="tag tag-green" style="margin-left:6px">Yours</span>@endif</div>
                                    <div style="font-size:12px;color:var(--ink-soft);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $w->academicYear->name }} · {{ $w->applicant_category }} · {{ $w->opens_at->format('d M Y') }} → {{ $w->closes_at->format('d M Y') }}</div>
                                    <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                        @if($status==='OPEN')<span class="tag tag-green">OPEN</span>@elseif($status==='CLOSING SOON')<span class="tag tag-gold">CLOSING SOON</span>@elseif($status==='CLOSED')<span class="tag tag-red">CLOSED</span>@else<span class="tag tag-blue">UPCOMING</span>@endif
                                        <span class="tag tag-grey">{{ $w->feeLabel() }}</span>
                                        <span class="cell-sub" style="font-size:11px;">{{ $w->timezone }}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex;gap:8px;align-items:center;flex:none;">
                                @if($w->isOpen() && !$isMine && in_array($w->academic_year_id, $myAppYearIds, true))
                                    <button type="button" disabled title="Only one application per academic year is allowed. You already applied in {{ $w->academicYear->name }}." style="background:var(--sand-200);color:var(--ink-soft);border:none;padding:8px 14px;border-radius:8px;font-weight:600;font-size:13px;cursor:not-allowed;opacity:.7;">Year applied — Closed</button>
                                @elseif($w->isOpen() && !$isMine)
                                    <form method="POST" action="{{ route('applicant.application.start', encId($w->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Start application','Start new application for this window?',()=>_f.submit())">@csrf<button class="btn btn-primary btn-sm">Apply</button></form>
                                @elseif($isMine) 
                                    <span class="tag tag-green">Applied</span>
                                @elseif($w->isClosed())
                                    <span class="tag tag-red">Closed</span>
                                @else
                                    <span class="tag tag-grey">Opens {{ $w->opens_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><strong>No windows</strong><p>Check back later.</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
