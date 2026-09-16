@extends('layouts.app')
@section('title','Entry Requirements')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <h1>Entry Requirements</h1>
            <p class="page-sub">Requirements are configured per programme by the admin.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('public.programmes') }}" class="btn btn-primary btn-sm">Browse Programmes</a>
        </div>
    </div>

    <div class="grid-2" style="align-items:start;">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">How requirements work</div>
                <span class="tag tag-blue">Per programme</span>
            </div>
            <div class="panel-body">
                <p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800);">Each programme has its own subject and grade requirements. Browse <a href="{{ route('public.programmes') }}" style="color:var(--terracotta-600);font-weight:700;">Programmes</a> and open a programme to see its entry requirements.</p>
                <div class="kv" style="margin-top:16px;">
                    <div class="kv-row"><span class="k">Principal passes</span><span class="v">2 at A-Level or equivalent</span></div>
                    <div class="kv-row"><span class="k">Minimum grade</span><span class="v">Per subject as listed</span></div>
                    <div class="kv-row"><span class="k">Departmental</span><span class="v">Additional where applicable</span></div>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">General criteria</div>
                <span class="tag tag-gold">Overview</span>
            </div>
            <div class="panel-body">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;gap:12px;align-items:flex-start;padding:12px;border:1px solid var(--line);border-radius:10px;background:var(--sand-100);">
                        <div class="thumb thumb-green" style="flex:none;">1</div>
                        <div><div style="font-weight:700;color:var(--coffee-900);font-size:13px;">Two principal passes</div><div class="muted" style="font-size:12.5px;margin-top:2px;">At A-Level or equivalent qualification.</div></div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;padding:12px;border:1px solid var(--line);border-radius:10px;background:var(--sand-100);">
                        <div class="thumb thumb-gold" style="flex:none;">2</div>
                        <div><div style="font-weight:700;color:var(--coffee-900);font-size:13px;">Minimum grade per subject</div><div class="muted" style="font-size:12.5px;margin-top:2px;">As listed on each programme page.</div></div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:flex-start;padding:12px;border:1px solid var(--line);border-radius:10px;background:var(--sand-100);">
                        <div class="thumb thumb-blue" style="flex:none;">3</div>
                        <div><div style="font-weight:700;color:var(--coffee-900);font-size:13px;">Additional departmental requirements</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Where applicable per faculty.</div></div>
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:10px;">
                    <a href="{{ route('public.programmes') }}" class="btn btn-primary" style="flex:1;">Browse Programmes</a>
                    <a href="{{ route('public.guidelines') }}" class="btn btn-ghost" style="flex:1;">Guidelines</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
