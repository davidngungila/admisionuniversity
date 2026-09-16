@extends('layouts.admin')
@section('title','Workflow Steps')
@section('content')
<div class="page-head">
    <div><h1>Application Workflow Steps</h1><p class="page-sub">Dynamic per admission level — Bachelor, Masters, PhD can each have a different sequence.</p></div>
    <a href="{{ route('admin.workflow.create') }}" class="btn btn-primary">+ New Step</a>
</div>
@forelse($steps as $levelName => $levelSteps)
    <div class="table-card" style="margin-bottom:16px">
        <div class="panel-head" style="background:var(--sand-100)"><div><div class="panel-title">{{ $levelName }}</div><div class="panel-sub">{{ $levelSteps->count() }} steps</div></div><span class="tag tag-blue">{{ $levelName }}</span></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th class="center">#</th><th>Step Name</th><th class="center">Route</th><th class="center">Required</th><th class="right">Actions</th></tr></thead>
                <tbody>
                    @foreach($levelSteps->sortBy('step_number') as $st)
                    <tr>
                        <td class="center"><span class="tag tag-gold">{{ $st->step_number }}</span></td>
                        <td><div class="cell-title" style="font-size:13px">{{ $st->step_name }}</div><div class="cell-sub">{{ $st->description ?? '—' }}</div></td>
                        <td class="center"><code style="background:var(--sand-100);border:1px solid var(--line);padding:3px 8px;border-radius:6px;font-size:12px">{{ $st->route }}</code></td>
                        <td class="center">@if($st->is_required)<span class="tag tag-green">Required</span>@else<span class="tag tag-grey">Optional</span>@endif</td>
                        <td class="right"><div class="row-actions">
                            <a href="{{ route('admin.workflow.edit', encId($st->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                            <form method="POST" action="{{ route('admin.workflow.destroy', encId($st->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Workflow Step','Are you sure you want to delete workflow step \'{{ $st->step_name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                        </div></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="table-card"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg></div><strong>No workflow steps yet</strong><p>Create your first step.</p></div></div>
@endforelse
@endsection
