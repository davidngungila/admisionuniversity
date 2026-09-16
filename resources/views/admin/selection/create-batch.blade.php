@extends('layouts.admin')
@section('title','Create Selection Batch')
@section('content')
<div class="page-head">
    <div><h1>Create Selection Batch</h1><p class="page-sub">Create a new selection batch for ranking and placement.</p></div>
    <div class="page-actions"><a href="{{ route('admin.selection.batches') }}" class="btn btn-ghost">← Back to Batches</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Batch Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.selection.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create selection batch','Are you sure you want to create this selection batch?',()=>_f.submit())">
            @csrf
            <div class="field">
                <label class="field-label">Name *</label>
                <input name="name" required placeholder="Batch 1 — Bachelor Round 2" value="{{ old('name') }}">
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Academic Year *</label>
                    <select name="academic_year_id" required>
                        <option value="">—</option>
                        @foreach($years as $y)<option value="{{ $y->id }}" @selected(old('academic_year_id')==$y->id)>{{ $y->name }}</option>@endforeach
                    </select>
                    @error('academic_year_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Round *</label>
                    <select name="application_round_id" required>
                        <option value="">—</option>
                        @foreach($rounds as $r)<option value="{{ $r->id }}" @selected(old('application_round_id')==$r->id)>{{ $r->academicYear->name }} — Round {{ $r->round_number }}</option>@endforeach
                    </select>
                    @error('application_round_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Admission Level (optional)</label>
                <select name="admission_level_id">
                    <option value="">All levels</option>
                    @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(old('admission_level_id')==$lv->id)>{{ $lv->name }}</option>@endforeach
                </select>
                <span class="field-hint">Leave empty to include all levels.</span>
                @error('admission_level_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.selection.batches') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Batch</button>
            </div>
        </form>
    </div>
</div>
@endsection
