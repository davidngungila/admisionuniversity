@extends('layouts.admin')
@section('title', $officer->exists ? 'Edit Support Officer' : 'New Support Officer')
@section('content')
<div class="page-head">
    <div><h1>{{ $officer->exists ? 'Edit Support Officer' : 'New Support Officer' }}</h1><p class="page-sub">Shown in the floating support helpline panel.</p></div>
    <div class="page-actions"><a href="{{ route('admin.support-officers.index') }}" class="btn btn-ghost">← Back</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Officer Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ $officer->exists ? route('admin.support-officers.update', encId($officer->id)) : route('admin.support-officers.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save officer','Save this support officer?',()=>_f.submit())">
            @csrf
            @if($officer->exists) @method('PUT') @endif
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" required value="{{ old('name', $officer->name) }}" placeholder="e.g. Support Team, TIZO MAVUNGE">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Phone Number *</label>
                    <input name="phone" required value="{{ old('phone', $officer->phone) }}" placeholder="0752811050" inputmode="tel">
                    @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Group / Category</label>
                    <input name="group_name" value="{{ old('group_name', $officer->group_name) }}" placeholder="e.g. Undergraduate Studies, Support Team">
                    @error('group_name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Designation</label>
                    <input name="designation" value="{{ old('designation', $officer->designation) }}" placeholder="e.g. Admissions Assistant">
                    @error('designation')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Order</label>
                    <input name="order_index" type="number" min="0" max="999" value="{{ old('order_index', $officer->order_index ?? 0) }}">
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $officer->exists ? $officer->is_active : true))> Active (shown on the site)</label>
                <label class="check-row" style="margin-top:8px"><input type="checkbox" name="is_online" value="1" @checked(old('is_online', $officer->exists ? $officer->is_online : true))> Online now (green badge)</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.support-officers.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">{{ $officer->exists ? 'Update Officer' : 'Create Officer' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection