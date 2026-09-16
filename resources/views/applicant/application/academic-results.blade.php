@extends('layouts.applicant')
@section('title','Academic Results')
@section('content')
<div class="page-head">
    <div>
        <h1>Academic Results</h1>
        <p class="page-sub">Step {{ $currentStep->step_number }} of {{ $steps->count() }} · Enter O-Level / A-Level / Diploma results</p>
    </div>
    <div class="page-actions">
        <span class="tag tag-blue">{{ $progress['completed'] }} of {{ $progress['total'] }} — {{ $progress['percent'] }}%</span>
    </div>
</div>

<div class="panel" style="margin-bottom:16px;">
    <div class="panel-body" style="padding:14px 18px;">
        <div style="height:6px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
            <div style="height:100%;background:var(--terracotta-600);width:{{ $progress['percent'] }}%"></div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            @foreach($steps as $st)
                @php $done = $application->stepCompletions()->where('workflow_step_id',$st->id)->whereNotNull('completed_at')->exists(); $isCurrent = $st->id===$currentStep->id; @endphp
                <span class="tag {{ $done ? 'tag-green' : ($isCurrent ? 'tag-terracotta' : 'tag-grey') }}" style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:16px;height:16px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;background:{{ $done ? 'var(--acacia-600)' : ($isCurrent ? 'var(--terracotta-600)' : 'var(--sand-200)') }};color:{{ $done || $isCurrent ? '#fff' : 'var(--coffee-700)' }};">{{ $done ? '✓' : $st->step_number }}</span>
                    {{ $st->step_name }}
                </span>
            @endforeach
        </div>
    </div>
</div>

@if($results->count())
    <div class="panel" style="margin-bottom:16px;">
        <div class="panel-head">
            <div class="panel-title">Saved Results</div>
            <span class="tag tag-green">{{ $results->count() }} saved</span>
        </div>
        <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
            @foreach($results as $r)
                <div class="panel" style="border:1.5px solid var(--line);box-shadow:none;">
                    <div class="panel-body" style="padding:14px 16px;">
                        <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                            <div>
                                <div style="font-weight:700;font-size:13.5px;color:var(--coffee-900);">{{ $r->exam_type }} — {{ $r->index_number }} ({{ $r->exam_year }})</div>
                                <div class="field-hint">{{ $r->school_name }}</div>
                            </div>
                            <span class="tag {{ $r->is_verified ? 'tag-green' : 'tag-grey' }}">{{ $r->is_verified ? 'Verified' : 'Pending verification' }}</span>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:10px;">
                            @foreach(($r->results ?? []) as $subj)
                                <div style="border:1px solid var(--line);border-radius:8px;padding:7px 10px;display:flex;justify-content:space-between;align-items:center;background:var(--sand-50);font-size:12.5px;">
                                    <span style="color:var(--coffee-700);font-weight:600;">{{ $subj['subject'] ?? '—' }}</span>
                                    <span class="tag tag-grey" style="padding:2px 8px;">{{ $subj['grade'] ?? '—' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
            <a href="{{ route('applicant.application.step', [encId($application->id), $steps->firstWhere('route','academic-results')->route]) }}?add=1" onclick="document.getElementById('new-result').scrollIntoView({behavior:'smooth'}); return false;" class="btn btn-ghost btn-sm" style="align-self:flex-start;">+ Add another result</a>
        </div>
    </div>
    <style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:repeat(2,1fr) !important} }</style>
@endif

<div class="panel" id="new-result">
    <div class="panel-head">
        <div>
            <div class="panel-title">Add Academic Result</div>
            <div class="panel-sub">You can add multiple exam sittings. Each must have at least one subject + grade.</div>
        </div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" style="display:flex;flex-direction:column;gap:18px;" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save academic results','Are you sure you want to save these academic results and continue?',()=>_f.submit())">
            @csrf
            <div id="results-container" style="display:flex;flex-direction:column;gap:18px;">
                <div class="result-block panel" style="background:var(--sand-50);border:1.5px solid var(--line);box-shadow:none;">
                    <div class="panel-body" style="display:flex;flex-direction:column;gap:14px;">
                        <div class="form-grid" style="grid-template-columns:repeat(4,1fr);">
                            <div class="field">
                                <label class="field-label">Exam Type *</label>
                                <select name="results[0][exam_type]" required>
                                    <option value="O-Level" selected>O-Level</option>
                                    <option value="A-Level">A-Level</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Certificate">Certificate</option>
                                    <option value="Bachelor">Bachelor</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">Index Number *</label>
                                <input name="results[0][index_number]" required placeholder="S0xxx/xxxx">
                            </div>
                            <div class="field">
                                <label class="field-label">Year *</label>
                                <input name="results[0][exam_year]" type="number" min="1980" max="{{ date('Y')+1 }}" value="{{ date('Y')-1 }}" required>
                            </div>
                            <div class="field">
                                <label class="field-label">School</label>
                                <input name="results[0][school_name]" placeholder="School name">
                            </div>
                        </div>
                        <div class="field">
                            <label class="field-label">Subjects &amp; Grades *</label>
                            <div class="subjects-list" style="display:flex;flex-direction:column;gap:8px;margin-top:6px;">
                                <div style="display:flex;gap:8px;">
                                    <input name="results[0][subjects][0][subject]" placeholder="e.g. Mathematics" required style="flex:1;">
                                    <select name="results[0][subjects][0][grade]" required style="width:110px;"><option value="A">A</option><option value="B">B</option><option value="C" selected>C</option><option value="D">D</option><option value="E">E</option><option value="F">F</option></select>
                                </div>
                                <div style="display:flex;gap:8px;">
                                    <input name="results[0][subjects][1][subject]" placeholder="e.g. Physics" required style="flex:1;">
                                    <select name="results[0][subjects][1][grade]" required style="width:110px;"><option value="A">A</option><option value="B">B</option><option value="C" selected>C</option><option value="D">D</option><option value="E">E</option><option value="F">F</option></select>
                                </div>
                            </div>
                            <button type="button" class="add-subject btn btn-ghost btn-sm" style="margin-top:8px;align-self:flex-start;">+ Add subject</button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="add-result" class="btn btn-ghost btn-sm" style="align-self:flex-start;">+ Add another exam sitting</button>

            <div class="form-actions">
                <a href="{{ route('applicant.application.show', encId($application->id)) }}" class="btn btn-ghost">Back</a>
                <button class="btn btn-primary">Save &amp; Continue →</button>
            </div>
        </form>
    </div>
</div>

<script>
let resultIndex = 1;
document.getElementById('add-result')?.addEventListener('click', () => {
    const container = document.getElementById('results-container');
    const block = container.firstElementChild.cloneNode(true);
    block.querySelectorAll('[name]').forEach(el => { el.name = el.name.replace('results[0]', `results[${resultIndex}]`); el.value = el.tagName === 'SELECT' ? el.options[0].value : ''; });
    block.querySelectorAll('.subjects-list').forEach(list => {
        list.innerHTML = `<div style="display:flex;gap:8px;"><input name="results[${resultIndex}][subjects][0][subject]" placeholder="Subject" required style="flex:1;"><select name="results[${resultIndex}][subjects][0][grade]" required style="width:110px;"><option value="A">A</option><option value="B">B</option><option value="C" selected>C</option><option value="D">D</option><option value="E">E</option><option value="F">F</option></select></div>`;
    });
    container.appendChild(block);
    resultIndex++;
});
document.addEventListener('click', (e) => {
    if (!e.target.classList.contains('add-subject')) return;
    const list = e.target.previousElementSibling;
    const idx = list.closest('.result-block').querySelector('[name*="[exam_type]"]').name.match(/results\[(\d+)\]/)[1];
    const count = list.children.length;
    const row = document.createElement('div');
    row.style.cssText = 'display:flex;gap:8px;';
    row.innerHTML = `<input name="results[${idx}][subjects][${count}][subject]" placeholder="Subject" required style="flex:1;"><select name="results[${idx}][subjects][${count}][grade]" required style="width:110px;"><option value="A">A</option><option value="B">B</option><option value="C" selected>C</option><option value="D">D</option><option value="E">E</option><option value="F">F</option></select>`;
    list.appendChild(row);
});
</script>
@endsection
