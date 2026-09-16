@extends('layouts.admin')
@section('title', $programme->name)
@section('content')
<div class="page-head">
    <div><h1>{{ $programme->name }}</h1><p class="page-sub">{{ $programme->admissionLevel->name }} · {{ $programme->code }}</p></div>
    <div class="page-actions">
        <a href="{{ route('admin.programmes.index') }}" class="btn btn-ghost">← Back to Programmes</a>
        <a href="{{ route('admin.programmes.edit', encId($programme->id)) }}" class="btn btn-primary">Edit Programme</a>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Programme Overview</div><span class="tag {{ $programme->status==='active' ? 'tag-green' : 'tag-grey' }}">{{ ucfirst($programme->status) }}</span></div>
    <div class="panel-body">
        <div class="kv">
            <div class="kv-row"><span class="k">Admission Level</span><span class="v">{{ $programme->admissionLevel->name }}</span></div>
            <div class="kv-row"><span class="k">Code</span><span class="v mono">{{ $programme->code }}</span></div>
            <div class="kv-row"><span class="k">Department</span><span class="v">{{ $programme->department->name }} ({{ $programme->department->faculty->name }})</span></div>
            <div class="kv-row"><span class="k">Campus</span><span class="v">{{ $programme->campus->name }}</span></div>
            <div class="kv-row"><span class="k">Duration</span><span class="v">{{ $programme->duration_years }} years · {{ $programme->study_mode }}</span></div>
            <div class="kv-row"><span class="k">Tuition</span><span class="v">TZS {{ number_format($programme->tuition_fee) }}</span></div>
            <div class="kv-row"><span class="k">Capacity</span><span class="v">{{ $programme->capacity ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Status</span><span class="v"><span class="tag {{ $programme->status==='active' ? 'tag-green' : 'tag-grey' }}">{{ $programme->status }}</span></span></div>
        </div>
        @if($programme->description)
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <p style="font-size:13.5px;color:var(--coffee-700);line-height:1.6;margin:0">{{ $programme->description }}</p>
            </div>
        @endif
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Entry Requirements</div></div>
    <div class="panel-body">
        @forelse($programme->requirements as $req)
            <div class="kv" style="margin-bottom:10px">
                <div class="kv-row"><span class="k">{{ $req->requirement_text ?? ($req->subject.' — Min '.$req->minimum_grade) }}</span><span class="v">@if($req->minimum_principal_passes)<span class="tag tag-grey">Passes: {{ $req->minimum_principal_passes }}</span>@endif</span></div>
            </div>
        @empty
            <div class="empty-state" style="padding:24px">
                <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                <strong>No requirements configured.</strong><p>Add entry requirements below.</p>
            </div>
        @endforelse

        <form method="POST" action="{{ route('admin.programmes.requirements.store', encId($programme->id)) }}" style="margin-top:18px" onsubmit="event.preventDefault(); const _f=this; confirmModal('Add requirement','Are you sure you want to add this requirement?',()=>_f.submit())">
            @csrf
            <div class="panel" style="background:var(--sand-50)">
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Subject</label>
                            <input name="subject" placeholder="Mathematics" value="{{ old('subject') }}">
                            @error('subject')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Min Grade</label>
                            <input name="minimum_grade" placeholder="D" value="{{ old('minimum_grade') }}">
                            @error('minimum_grade')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Text (optional)</label>
                            <input name="requirement_text" placeholder="Or full text" value="{{ old('requirement_text') }}">
                            @error('requirement_text')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field" style="justify-content:flex-end">
                            <button class="btn btn-primary" style="width:100%">Add Requirement</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
