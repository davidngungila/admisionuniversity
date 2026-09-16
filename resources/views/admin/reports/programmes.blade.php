@extends('layouts.admin')
@section('title','Programmes Report')
@section('content')
<div class="page-head">
    <div><h1>Programmes Report</h1><p class="page-sub">Applications per programme and capacity overview.</p></div>
    <div class="page-actions"><a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">← Reports Overview</a></div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Programme</th><th class="center">Level</th><th class="right">Applications</th><th class="right">Capacity</th></tr></thead>
            <tbody>
                @forelse($programmes as $p)
                    <tr>
                        <td><div class="cell-main"><div class="thumb thumb-blue">{{ substr($p->code,0,2) }}</div><div><div class="cell-title" style="font-size:13px">{{ $p->name }}</div><div class="cell-sub mono">{{ $p->code }}</div></div></div></td>
                        <td class="center"><span class="tag tag-blue">{{ $p->admissionLevel->name }}</span></td>
                        <td class="right"><span class="tag tag-terracotta">{{ $p->application_programmes_count }}</span></td>
                        <td class="right"><span class="muted">{{ $p->capacity ?? '—' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty-state" style="padding:32px"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 4 2z"/></svg></div><strong>No programmes</strong><p>No programme data available.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
