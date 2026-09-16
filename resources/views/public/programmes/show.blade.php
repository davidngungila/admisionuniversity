@extends('layouts.app')
@section('title', $programme->name)
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <a href="{{ route('public.programmes') }}" class="btn btn-ghost btn-sm" style="margin-bottom:10px;">← Back to programmes</a>
            <h1>{{ $programme->name }}</h1>
            <p class="page-sub">{{ $programme->admissionLevel->name }} · {{ $programme->code }} · {{ $programme->department->faculty->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('register') }}" class="btn btn-primary">Apply Now</a>
            <a href="{{ route('public.programmes') }}" class="btn btn-ghost">Browse more</a>
        </div>
    </div>

    <div class="grid-2" style="align-items:start;">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Programme Details</div>
                <span class="tag tag-terracotta">{{ $programme->admissionLevel->name }}</span>
            </div>
            <div class="panel-body">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                    <div class="thumb thumb-terracotta" style="width:48px;height:48px;font-size:15px;">{{ strtoupper(substr($programme->code,0,2)) }}</div>
                    <div>
                        <div style="font-weight:800;color:var(--coffee-900);font-size:16px;line-height:1.2;">{{ $programme->name }}</div>
                        <div style="font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft);margin-top:2px;">{{ $programme->code }} · {{ $programme->campus->name }}</div>
                    </div>
                </div>
                <div class="kv">
                    <div class="kv-row"><span class="k">Level</span><span class="v">{{ $programme->admissionLevel->name }}</span></div>
                    <div class="kv-row"><span class="k">Duration</span><span class="v">{{ $programme->duration_years }} Years</span></div>
                    <div class="kv-row"><span class="k">Campus</span><span class="v">{{ $programme->campus->name }}</span></div>
                    <div class="kv-row"><span class="k">Study Mode</span><span class="v"><span class="tag tag-grey">{{ $programme->study_mode }}</span></span></div>
                    <div class="kv-row"><span class="k">Department</span><span class="v">{{ $programme->department->name }}</span></div>
                    <div class="kv-row"><span class="k">Faculty</span><span class="v">{{ $programme->department->faculty->name }}</span></div>
                    <div class="kv-row"><span class="k">Tuition</span><span class="v" style="color:var(--terracotta-600);">TZS {{ number_format($programme->tuition_fee) }}</span></div>
                    <div class="kv-row"><span class="k">Capacity</span><span class="v">{{ $programme->capacity ?? '—' }}</span></div>
                </div>
                @if($programme->description)
                    <div style="margin-top:16px;padding:14px;background:var(--sand-100);border:1px solid var(--line);border-radius:10px;font-size:13.5px;line-height:1.6;color:var(--coffee-800);">{{ $programme->description }}</div>
                @endif
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">At a Glance</div>
                </div>
                <div class="panel-body">
                    <div class="stat-grid" style="margin-bottom:0;">
                        <div class="stat-card" style="padding:14px;">
                            <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg></div></div>
                            <div class="stat-label">Duration</div>
                            <div class="stat-value" style="font-size:20px;">{{ $programme->duration_years }} Years</div>
                            <div class="stat-sub">{{ $programme->study_mode }}</div>
                        </div>
                        <div class="stat-card" style="padding:14px;">
                            <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg></div></div>
                            <div class="stat-label">Tuition</div>
                            <div class="stat-value" style="font-size:18px;">TZS {{ number_format($programme->tuition_fee) }}</div>
                            <div class="stat-sub">per year</div>
                        </div>
                    </div>
                    <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
                        <span class="tag tag-blue">{{ $programme->campus->name }}</span>
                        <span class="tag tag-green">{{ $programme->department->name }}</span>
                        <span class="tag tag-coffee">Capacity {{ $programme->capacity ?? '—' }}</span>
                    </div>
                    <div class="form-actions" style="margin-top:16px;padding-top:14px;">
                        <a href="{{ route('register') }}" class="btn btn-primary" style="flex:1;">Apply Now</a>
                        <a href="{{ route('public.programmes') }}" class="btn btn-ghost" style="flex:1;">Browse more</a>
                    </div>
                </div>
            </div>

            @if($programme->requirements->count())
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-title">Entry Requirements</div>
                        <span class="tag tag-grey">{{ $programme->requirements->count() }} items</span>
                    </div>
                    <div class="panel-body">
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            @foreach($programme->requirements as $req)
                                <div class="kv" style="padding:0;">
                                    <div class="kv-row" style="align-items:center;">
                                        <span class="k" style="font-weight:500;color:var(--coffee-800);flex:1;">{{ $req->requirement_text ?? ($req->subject ? $req->subject.' — Min grade '.$req->minimum_grade : 'Principal passes: '.$req->minimum_principal_passes) }}</span>
                                        @if($req->minimum_grade)<span class="tag tag-gold">Min: {{ $req->minimum_grade }}</span>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="panel">
                    <div class="panel-body">
                        <div class="empty-state" style="padding:24px 16px;">
                            <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M10 13H8M16 17H8M13 13h2"/></svg></div>
                            <strong>No specific requirements listed</strong>
                            <p>Contact admissions for details.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
