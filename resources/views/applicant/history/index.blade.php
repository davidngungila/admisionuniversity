@extends('layouts.applicant')
@section('title','Programmes Applied')
@section('content')
<div class="page-head">
    <div>
        <h1>Programmes Applied</h1>
        <p class="page-sub">History across rounds and academic years.</p>
    </div>
</div>

@if($grouped->isEmpty())
    <div class="panel">
        <div class="panel-body">
            <div class="empty-state">
                <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                <strong>No applications yet</strong>
                <p>Start from the dashboard to create your first application.</p>
            </div>
        </div>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach($grouped as $label => $apps)
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">{{ $label }}</div>
                    <span class="tag tag-grey">{{ count($apps) }} application(s)</span>
                </div>
                <div class="panel-body" style="display:flex;flex-direction:column;gap:16px;">
                    @foreach($apps as $app)
                        <div class="panel" style="margin:0;box-shadow:none;border:1px solid var(--line);background:var(--sand-50);">
                            <div class="panel-body" style="padding:14px 16px;">
                                <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:10px;">
                                    <span class="field-hint">Application <span class="mono" style="font-weight:700;color:var(--coffee-900);">{{ $app->application_number ?? '#'.$app->id }}</span></span>
                                    @php $tag = match($app->status){ 'SUBMITTED'=>'tag-blue','SELECTED'=>'tag-green','ADMITTED'=>'tag-green','REJECTED'=>'tag-red','WAITLISTED'=>'tag-gold', default=>'tag-grey'}; @endphp
                                    <span class="tag {{ $tag }}">{{ $app->status }}</span>
                                </div>
                                @if($app->selectedProgrammes->sortBy('preference_order')->count())
                                    <div class="kv" style="background:var(--white);">
                                        @foreach($app->selectedProgrammes->sortBy('preference_order') as $ap)
                                            <div class="kv-row">
                                                <span class="k">{{ $ap->preference_order }}. {{ $ap->programme->name }}</span>
                                                <span class="v" style="color:var(--ink-soft);">{{ $ap->programme->code }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state" style="padding:16px;"><strong>No programmes selected yet</strong></div>
                                @endif
                                <a href="{{ route('applicant.application.status', encId($app->id)) }}" class="btn btn-ghost btn-sm" style="margin-top:12px;">View application →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
