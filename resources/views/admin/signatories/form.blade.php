@extends('layouts.admin')
@section('title', $signatory->exists ? 'Edit Signatory' : 'New Signatory')
@section('content')
<div class="page-head">
    <div><h1>{{ $signatory->exists ? 'Edit Signatory' : 'New Signatory' }}</h1><p class="page-sub">Signatures are attached to admission letters and joining instructions.</p></div>
    <div class="page-actions"><a href="{{ route('admin.signatories.index') }}" class="btn btn-ghost">← Back to Signatories</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Signatory Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ $signatory->exists ? route('admin.signatories.update', encId($signatory->id)) : route('admin.signatories.store') }}" enctype="multipart/form-data" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save signatory','Save this signatory?',()=>_f.submit())">
            @csrf
            @if($signatory->exists) @method('PUT') @endif
            <div class="field">
                <label class="field-label">Full Name *</label>
                <input name="name" required value="{{ old('name', $signatory->name) }}">
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Title</label>
                    <input name="title" value="{{ old('title', $signatory->title) }}" placeholder="e.g. Prof., Dr., Mr.">
                    @error('title')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Order</label>
                    <input name="order_index" type="number" min="0" max="999" value="{{ old('order_index', $signatory->order_index ?? 0) }}">
                    <span class="field-hint">Smallest appears first (left).</span>
                    @error('order_index')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Designation</label>
                <input name="designation" value="{{ old('designation', $signatory->designation) }}" placeholder="e.g. Registrar — Academic Affairs">
                @error('designation')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Signature Image</label>
                <input type="file" name="signature_image" accept="image/*">
                @error('signature_image')<span class="field-err">{{ $message }}</span>@enderror
                @if($signatory->signature_image && file_exists(public_path($signatory->signature_image)))
                    <div style="margin-top:10px;display:flex;align-items:center;gap:12px;">
                        <img src="{{ asset($signatory->signature_image) }}" style="height:40px;object-fit:contain;border:1px solid var(--line);border-radius:8px;padding:6px;background:#fff;" alt="Current signature">
                        <span class="field-hint">Current signature — upload a new file to replace it.</span>
                    </div>
                @endif
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $signatory->exists ? $signatory->is_active : true))> Active on documents</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.signatories.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">{{ $signatory->exists ? 'Update Signatory' : 'Create Signatory' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection