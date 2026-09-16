@extends('layouts.app')
@section('title','Academic Programmes')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <h1>Academic Programmes</h1>
            <p class="page-sub">Browse programmes by level, campus and study mode.</p>
        </div>
        <div class="page-actions">
            <span class="tag tag-grey">{{ $programmes->total() }} programmes</span>
        </div>
    </div>

    <div class="panel" style="margin-bottom:18px;">
        <div class="panel-head">
            <div class="panel-title">Filters</div>
            <div class="panel-sub">Refine results</div>
        </div>
        <div class="panel-body">
            <form method="GET">
                <div class="filters-row" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                    <div class="field" style="flex:1.6;min-width:160px;">
                        <label class="field-label">Search</label>
                        <input name="search" value="{{ request('search') }}" placeholder="Computer, Engineering…">
                    </div>
                    <div class="field" style="flex:1;min-width:140px;">
                        <label class="field-label">Level</label>
                        <select name="level">
                            <option value="">All</option>
                            @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(request('level')==$lv->id)>{{ $lv->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="field" style="flex:1;min-width:140px;">
                        <label class="field-label">Campus</label>
                        <select name="campus">
                            <option value="">All</option>
                            @foreach($campuses as $c)<option value="{{ $c->id }}" @selected(request('campus')==$c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="field" style="flex:1;min-width:140px;">
                        <label class="field-label">Study Mode</label>
                        <select name="study_mode">
                            <option value="">All</option>
                            <option value="Full Time" @selected(request('study_mode')=='Full Time')>Full Time</option>
                            <option value="Part Time" @selected(request('study_mode')=='Part Time')>Part Time</option>
                            <option value="Distance" @selected(request('study_mode')=='Distance')>Distance</option>
                        </select>
                    </div>
                    <div class="field" style="flex:0 0 110px;min-width:110px;">
                        <label class="field-label">&nbsp;</label>
                        <button class="btn btn-primary" style="width:100%;height:42px;">SEARCH</button>
                    </div>
                </div>
                @if(request()->hasAny(['search','level','campus','study_mode']))
                    <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                        @if(request('search'))<span class="tag tag-blue">Search: {{ request('search') }}</span>@endif
                        @if(request('level'))<span class="tag tag-terracotta">Level: {{ $levels->firstWhere('id', request('level'))?->name }}</span>@endif
                        @if(request('campus'))<span class="tag tag-green">Campus: {{ $campuses->firstWhere('id', request('campus'))?->name }}</span>@endif
                        @if(request('study_mode'))<span class="tag tag-gold">{{ request('study_mode') }}</span>@endif
                        <a href="{{ route('public.programmes') }}" class="tag tag-grey" style="text-decoration:none;">Clear filters ×</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <div class="chip-filters">
                <button class="chip active" data-filter="all">All</button>
                <button class="chip" data-filter="bachelor">Bachelor</button>
                <button class="chip" data-filter="diploma">Diploma</button>
                <button class="chip" data-filter="certificate">Certificate</button>
                <button class="chip" data-filter="masters">Masters</button>
            </div>
            <div class="table-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="tableSearch" type="text" placeholder="Filter in table…">
            </div>
        </div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Code</th><th>Programme</th><th class="center">Level</th><th class="center">Duration</th><th>Campus</th><th class="center">Mode</th><th class="right">Tuition</th><th class="right">Action</th></tr></thead>
                <tbody>
                    @forelse($programmes as $p)
                    <tr data-filter="{{ strtolower($p->admissionLevel->short_name) }}">
                        <td><span class="cell-mono">{{ $p->code }}</span></td>
                        <td><div class="cell-main"><div class="thumb thumb-terracotta">{{ strtoupper(substr($p->code,0,2)) }}</div><div><div class="cell-title" style="font-size:13px">{{ $p->name }}</div><div class="cell-sub">{{ $p->department->name ?? '' }}</div></div></div></td>
                        <td class="center"><span class="tag tag-blue">{{ $p->admissionLevel->short_name }}</span></td>
                        <td class="center"><span class="tag tag-grey">{{ $p->duration_years }}y</span></td>
                        <td><span class="cell-sub">{{ $p->campus->name }}</span></td>
                        <td class="center"><span class="tag tag-grey">{{ $p->study_mode }}</span></td>
                        <td class="right"><span class="tag tag-gold">TZS {{ number_format($p->tuition_fee) }}</span></td>
                        <td class="right"><a href="{{ route('public.programmes.show', encId($p->id)) }}" class="btn btn-ghost btn-sm">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-3.5-3.5"/></svg></div><strong>No programmes found.</strong><p>Try adjusting your filters.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programmes->hasPages())
            <div class="table-pagination">
                <div class="pager-info">Showing {{ $programmes->firstItem() }}–{{ $programmes->lastItem() }} of {{ $programmes->total() }}</div>
                <div class="pager-pages">{{ $programmes->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
</div>
<style>
@media(min-width:901px){ .filters-row{flex-wrap:nowrap !important;} }
@media(max-width:900px){ .filters-row .field{flex:1 1 160px !important;} }
</style>
@endsection
