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
                            <span class="tag {{ $r->is_verified ? (str_ends_with($r->exam_body ?? '', '(BYPASSED)') ? 'tag-gold' : 'tag-green') : 'tag-grey' }}">{{ $r->is_verified ? (str_ends_with($r->exam_body ?? '', '(BYPASSED)') ? 'Verified (BYPASSED)' : 'Verified') : 'Pending verification' }}</span>
                        </div>
                        <div class="ar-results-grid" style="display:grid;gap:8px;margin-top:10px;">
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
    <style>
        .ar-grid{grid-template-columns:repeat(4,1fr)}
        .subj-row{display:flex;gap:8px;align-items:center}
        .subj-input{flex:1;min-width:0}
        .subj-row .subj-grade{flex:none;width:110px}
        .ar-results-grid{grid-template-columns:repeat(4,1fr)}
        @media(max-width:1100px){.ar-grid,.ar-results-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:640px){
            .ar-grid,.ar-results-grid{grid-template-columns:1fr}
            .subj-row{flex-direction:column;align-items:stretch}
            .subj-row .subj-grade{width:100%}
        }
    </style>
@endif

<div class="panel" id="new-result">
    <div class="panel-head">
        <div>
            <div class="panel-title">Add Academic Result</div>
            <div class="panel-sub">You can add multiple exam sittings. Each must have at least one subject + grade.</div>
        </div>
    </div>
<div class="panel-body">
        <form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" style="display:flex;flex-direction:column;gap:18px;" onsubmit="event.preventDefault(); if(!validateAcademicForm(this)) return; confirmModal('Save academic results','Are you sure you want to save these academic results and continue?',()=>this.submit())">
            @csrf
            <div id="results-container" style="display:flex;flex-direction:column;gap:18px;">
                <div class="result-block panel" style="background:var(--sand-50);border:1.5px solid var(--line);box-shadow:none;">
                    <div class="panel-body" style="display:flex;flex-direction:column;gap:14px;">
                        <input type="hidden" class="ar-exam-body" name="results[0][exam_body]" value="">
                        <input type="hidden" class="ar-verified" name="results[0][is_verified]" value="0">
                        <input type="hidden" class="ar-year" name="results[0][exam_year]" value="">
                        <input type="hidden" class="ar-school" name="results[0][school_name]" value="">

                        <div class="form-grid ar-grid">
                            <div class="field">
                                <label class="field-label">Exam Type *</label>
                                <select name="results[0][exam_type]" class="ar-exam-type" required onchange="applyExamTypeUI(this.closest('.result-block'))">
                                    <option value="O-Level" selected>O-Level</option>
                                    <option value="A-Level">A-Level</option>
                                    <option value="Certificate">Certificate</option>
                                    <option value="Diploma">Diploma</option>
                                </select>
                            </div>
                            <div class="field ar-identifier-field">
                                <label class="field-label ar-identifier-label">Index Number *</label>
                                <input name="results[0][index_number]" class="ar-identifier" required placeholder="e.g. S0105/0013/2023">
                            </div>
                        </div>

                        <div class="field">
                            <div class="field-label" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">Subjects &amp; Grades *
                                <span class="tag tag-green ar-verified-tag" style="display:none;">✓ Verified by <span class="ar-provider-name"></span></span>
                            </div>
                            <div class="subjects-list" style="display:flex;flex-direction:column;gap:8px;margin-top:6px;">
                                <div class="ar-placeholder" style="border:1px dashed var(--line);border-radius:8px;padding:10px 12px;background:#fff;font-size:12.5px;color:var(--coffee-600);">Enter your NECTA index number above and click <strong>Fetch from NECTA</strong> to load your official results.</div>
                            </div>
                            <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                                <button type="button" class="btn btn-primary btn-sm ar-fetch-btn" onclick="fetchOfficialResult(this.closest('.result-block'))">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span class="ar-fetch-label">Fetch from NECTA</span>
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm ar-change-btn" style="display:none;" onclick="resetBlockVerified(this.closest('.result-block'))">Change details</button>
                            </div>
                            <div class="ar-err" style="display:none;color:#b42318;background:#fef3f2;border:1px solid #fecdca;border-radius:8px;padding:8px 12px;font-size:12.5px;margin-top:10px;"></div>
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

<div class="modal-backdrop" id="arAlertBackdrop" onclick="if(event.target===this)closeModal('arAlertBackdrop')">
    <div class="modal modal-sm">
        <div class="modal-body" style="text-align:center;padding:28px 22px 18px">
            <div class="es-icon" style="background:var(--terracotta-100);border-color:#e8b4b0;color:var(--terracotta-600);margin-bottom:14px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h3 style="font-size:15px;font-weight:800;color:var(--coffee-900)">Please check</h3>
            <p id="arAlertMsg" style="font-size:13px;color:var(--ink-soft);margin-top:6px;line-height:1.5"></p>
        </div>
        <div class="modal-foot" style="justify-content:center">
            <button type="button" class="btn btn-primary btn-sm" onclick="closeModal('arAlertBackdrop')">OK</button>
        </div>
    </div>
</div>

<script>
const FETCH_ROUTE = "{{ route('applicant.application.results.fetch', encId($application->id)) }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const EXAM_META = {
    'O-Level': { label: 'Index Number', placeholder: 'e.g. S0105/0013/2023', fetch: 'Fetch from NECTA', provider: 'NECTA', hint: 'Enter your NECTA index number above and click <strong>Fetch from NECTA</strong> to load your official results.' },
    'A-Level': { label: 'Index Number', placeholder: 'e.g. S0105/0013/2023', fetch: 'Fetch from NECTA', provider: 'NECTA', hint: 'Enter your NECTA index number above and click <strong>Fetch from NECTA</strong> to load your official results.' },
    'Certificate': { label: 'Registration Number', placeholder: 'e.g. FT/2023/000123', fetch: 'Fetch from NACTVET', provider: 'NACTVET', hint: 'Enter your NACTVET registration number above, then click <strong>Fetch from NACTVET</strong> to load your results.' },
    'Diploma': { label: 'AVN Number', placeholder: 'e.g. AVN/2020/000456', fetch: 'Fetch from NACTVET', provider: 'NACTVET', hint: 'Enter your AVN number above, then click <strong>Fetch from NACTVET</strong> to load your results.' }
};

function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
}
function blockIndex(block) {
    const m = block.querySelector('[name*="[exam_type]"]').name.match(/results\[(\d+)\]/);
    return m ? m[1] : '0';
}
function showError(el, msg) { el.textContent = msg; el.style.display = ''; }
function arAlert(msg) {
    document.getElementById('arAlertMsg').textContent = msg;
    openModal('arAlertBackdrop');
}

function applyExamTypeUI(block) {
    const meta = EXAM_META[block.querySelector('.ar-exam-type').value] || EXAM_META['O-Level'];
    block.querySelector('.ar-identifier-label').textContent = meta.label + ' *';
    block.querySelector('.ar-identifier').placeholder = meta.placeholder;
    block.querySelector('.ar-fetch-label').textContent = meta.fetch;
    block.querySelector('.subjects-list').innerHTML = '<div class="ar-placeholder" style="border:1px dashed var(--line);border-radius:8px;padding:10px 12px;background:#fff;font-size:12.5px;color:var(--coffee-600);">' + meta.hint + '</div>';
    const err = block.querySelector('.ar-err'); err.style.display = 'none';
    setBlockFields(block, {});
    block.querySelector('.ar-year').value = '';
    block.querySelector('.ar-school').value = '';
    block.querySelector('.ar-change-btn').style.display = 'none';
}

function setBlockFields(block, data) {
    block.querySelector('.ar-exam-body').value = data.provider || '';
    block.querySelector('.ar-verified').value = data.verified ? '1' : '0';
    if (data.provider) block.querySelector('.ar-provider-name').textContent = data.provider;
    block.querySelector('.ar-verified-tag').style.display = data.verified ? '' : 'none';
    block.querySelector('.ar-fetch-btn').disabled = !!data.verified;
    if (data.verified) block.querySelector('.ar-fetch-label').textContent = 'Fetched ✓';
    block.querySelector('.ar-exam-type').disabled = !!data.verified;
    block.querySelector('.ar-identifier').readOnly = !!data.verified;
}

async function fetchOfficialResult(block) {
    const err = block.querySelector('.ar-err');
    err.style.display = 'none';

    const type = block.querySelector('.ar-exam-type').value;
    const identifier = block.querySelector('.ar-identifier').value.trim();
    const meta = EXAM_META[type];

    const payload = { exam_type: type };
    if (type === 'Certificate') {
        payload.registration_number = identifier;
    } else if (type === 'Diploma') {
        payload.avn_number = identifier;
    } else {
        payload.index_number = identifier;
    }

    const btn = block.querySelector('.ar-fetch-btn');
    const lab = btn.querySelector('.ar-fetch-label');
    const original = lab.textContent;
    btn.disabled = true; lab.textContent = 'Fetching…';

    try {
        const res = await fetch(FETCH_ROUTE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.ok) {
            const msg = data.error || 'Could not fetch results. Please check the details and try again.';
            showError(err, msg);
            arAlert(msg);
            btn.disabled = false; lab.textContent = original;
            return;
        }

        const list = block.querySelector('.subjects-list');
        const idx = blockIndex(block);
        list.innerHTML = '';
        data.subjects.forEach((s, n) => {
            const row = document.createElement('div');
            row.className = 'subj-row';
            row.innerHTML = '<input class="subj-input" name="results[' + idx + '][subjects][' + n + '][subject]" value="' + esc(s.subject) + '" readonly>'
                + '<span class="tag tag-green" style="flex:none;">' + esc(s.grade) + '</span>'
                + '<input type="hidden" name="results[' + idx + '][subjects][' + n + '][grade]" value="' + esc(s.grade) + '">';
            list.appendChild(row);
        });

        if (data.identifier) block.querySelector('.ar-identifier').value = data.identifier;
        if (data.exam_year) block.querySelector('.ar-year').value = data.exam_year;
        if (data.school_name) block.querySelector('.ar-school').value = data.school_name;

        setBlockFields(block, { provider: data.provider, verified: true });
        block.querySelector('.ar-change-btn').style.display = '';
        block.querySelector('.ar-change-btn').textContent = meta.provider === 'NECTA' ? 'Change index number' : 'Change details';
    } catch (e) {
        const msg = 'Network error. Please check your connection and try again.';
        showError(err, msg);
        arAlert(msg);
        btn.disabled = false; lab.textContent = original;
    }
}

function resetBlockVerified(block) {
    block.querySelectorAll('[readonly]').forEach(el => el.readOnly = false);
    block.querySelector('.ar-exam-type').disabled = false;
    block.querySelector('.ar-exam-body').value = '';
    block.querySelector('.ar-verified').value = '0';
    applyExamTypeUI(block);
}

function validateAcademicForm(form) {
    const missing = [];
    form.querySelectorAll('.result-block').forEach(block => {
        if (block.querySelector('.ar-verified').value !== '1') {
            missing.push(+blockIndex(block) + 1);
        }
    });
    if (missing.length) {
        alert('Every exam sitting must be verified from the official source (NECTA / NACTVET). Please fetch results for sitting(s): ' + missing.join(', '));
        return false;
    }
    return true;
}

let resultIndex = 1;
document.getElementById('add-result')?.addEventListener('click', () => {
    const container = document.getElementById('results-container');
    const block = container.firstElementChild.cloneNode(true);
    block.querySelectorAll('[name]').forEach(el => { el.name = el.name.replace('results[0]', `results[${resultIndex}]`); el.value = el.tagName === 'SELECT' ? el.options[0].value : ''; });
    applyExamTypeUI(block);
    container.appendChild(block);
    resultIndex++;
});

document.querySelectorAll('.result-block').forEach(applyExamTypeUI);
</script>
@endsection
