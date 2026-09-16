@extends('layouts.applicant')
@section('title','Apply for Programme')
@section('content')
<div class="page-head">
    <div>
        <h1>Apply for Programme</h1>
        <p class="page-sub">Step {{ $currentStep->step_number }} of {{ $steps->count() }} · Choose up to 3 programmes — set First, Second, Third choice in the right panel.</p>
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

<form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" id="prog-form" onsubmit="const c=document.querySelectorAll('.selected-item').length; if(c===0){event.preventDefault();alert('Select at least 1 programme.');return false;} if(c>3){event.preventDefault();alert('Maximum 3 programmes.');return false;} event.preventDefault();confirmModal('Save programme selection','Are you sure you want to save your programme selection and continue?',()=>this.submit())">
    @csrf

    <div class="grid-2">
        {{-- LEFT: Available Courses --}}
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Available Courses</div>
                <span class="tag tag-grey"><span id="avail-count">0</span> available</span>
            </div>
            <div class="panel-body" style="padding:0;">
                <div style="padding:12px 16px;border-bottom:1px solid var(--line);">
                    <div class="table-search" style="width:100%;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="prog-search" placeholder="Search code or name…" style="width:100%;">
                    </div>
                </div>
                <div id="available-list" style="max-height:400px;overflow-y:auto;">
                    @forelse($programmesByLevel as $p)
                        @php $isSelected = $selected->pluck('programme_id')->contains($p->id); @endphp
                        <div class="avail-item" data-id="{{ $p->id }}" data-name="{{ strtolower($p->name.' '.$p->code.' '.$p->campus->name) }}" style="display:{{ $isSelected ? 'none' : 'flex' }};align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--line);">
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;font-size:13px;line-height:1.3;color:var(--coffee-900);">{{ $p->name }}</div>
                                <div style="font-size:11.5px;color:var(--ink-soft);margin-top:3px;">{{ $p->code }} · {{ $p->duration_years }}y · {{ $p->study_mode }} · {{ $p->campus->name }} · TZS {{ number_format($p->tuition_fee) }}</div>
                            </div>
                            <button type="button" class="add-btn btn btn-primary btn-sm" style="flex:none;white-space:nowrap;" data-id="{{ $p->id }}">Add →</button>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:32px 20px;">
                            <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                            <strong>No programmes available</strong>
                            <p>No programmes available for this level.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Priority Selection --}}
        <div class="panel" style="background:var(--sand-50);">
            <div class="panel-head" style="background:var(--white);">
                <div class="panel-title">My Selection — Priority Order</div>
                <span class="tag tag-green"><span id="sel-count">{{ $selected->count() }}</span> / 3</span>
            </div>
            <div class="panel-body">
                <div id="selected-list" style="display:flex;flex-direction:column;gap:10px;min-height:200px;">
                    @forelse($selected->sortBy('preference_order') as $idx => $s)
                        <div class="selected-item panel" data-id="{{ $s->programme->id }}" style="margin:0;box-shadow:none;border:1.5px solid var(--line);background:var(--white);">
                            <div class="panel-body" style="display:flex;align-items:center;gap:12px;padding:12px 14px;">
                                <div class="priority-badge" style="width:36px;height:36px;border-radius:50%;background:var(--coffee-900);color:var(--gold-500);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;flex:none;">{{ $idx+1 }}</div>
                                <div style="flex:1;min-width:0;">
                                    <div class="priority-label" style="font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--terracotta-600);">
                                        @if($idx==0) First Choice @elseif($idx==1) Second Choice @else Third Choice @endif
                                    </div>
                                    <div style="font-weight:700;font-size:13px;color:var(--coffee-900);line-height:1.3;">{{ $s->programme->name }}</div>
                                    <div style="font-size:11.5px;color:var(--ink-soft);">{{ $s->programme->code }} · {{ $s->programme->campus->name }}</div>
                                </div>
                                <button type="button" class="remove-btn btn-icon danger" data-id="{{ $s->programme->id }}" title="Remove" style="flex:none;">×</button>
                            </div>
                        </div>
                    @empty
                        <div id="empty-selected" class="empty-state" style="border:1.5px dashed var(--line);border-radius:12px;background:var(--white);padding:32px 16px;">
                            <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                            <strong>No programme selected yet</strong>
                            <p>Pick from the left — max 3.</p>
                        </div>
                    @endforelse
                </div>

                <div id="programme-inputs" style="margin-top:12px;">
                    @foreach($selected->sortBy('preference_order') as $s)
                        <input type="hidden" name="programmes[]" value="{{ $s->programme->id }}" data-id="{{ $s->programme->id }}">
                    @endforeach
                </div>

                <p class="field-hint" style="margin-top:10px;">Order in this panel = priority. Top is First Choice. Use Remove then re-Add to reorder.</p>
                @error('programmes')<span class="field-err">{{ $message }}</span>@enderror
                @error('programmes.*')<span class="field-err">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="form-actions" style="margin-top:16px;">
        <a href="{{ route('applicant.application.show', encId($application->id)) }}" class="btn btn-ghost">Back</a>
        <button class="btn btn-primary">Save &amp; Continue →</button>
    </div>
</form>

<script>
const availableList = document.getElementById('available-list');
const selectedList = document.getElementById('selected-list');
const programmeInputs = document.getElementById('programme-inputs');
const searchEl = document.getElementById('prog-search');
const selCountEl = document.getElementById('sel-count');
const availCountEl = document.getElementById('avail-count');

function updateCounts(){
    const sel = selectedList.querySelectorAll('.selected-item').length;
    const avail = availableList.querySelectorAll('.avail-item:not([style*="display: none"])').length;
    if(selCountEl) selCountEl.textContent = sel;
    if(availCountEl) availCountEl.textContent = avail;
    document.querySelectorAll('.add-btn').forEach(b=>{
        const inSel = selectedList.querySelector(`.selected-item[data-id="${b.dataset.id}"]`);
        b.disabled = !!inSel || sel >= 3;
        b.style.opacity = b.disabled ? '0.4' : '1';
        b.style.pointerEvents = b.disabled ? 'none' : 'auto';
    });
    const empty = document.getElementById('empty-selected');
    if(empty) empty.style.display = sel === 0 ? 'block' : 'none';
}

function refreshPriorities(){
    selectedList.querySelectorAll('.selected-item').forEach((el, idx)=>{
        const badge = el.querySelector('.priority-badge');
        const label = el.querySelector('.priority-label');
        if(badge) badge.textContent = idx+1;
        if(label){
            label.textContent = idx===0 ? 'First Choice' : idx===1 ? 'Second Choice' : 'Third Choice';
        }
    });
    programmeInputs.innerHTML = '';
    selectedList.querySelectorAll('.selected-item').forEach(el=>{
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'programmes[]';
        inp.value = el.dataset.id;
        inp.dataset.id = el.dataset.id;
        programmeInputs.appendChild(inp);
    });
    updateCounts();
}

function createSelectedItem(progId, name, code, campus){
    const div = document.createElement('div');
    div.className = 'selected-item panel';
    div.dataset.id = progId;
    div.style.cssText = 'margin:0;box-shadow:none;border:1.5px solid var(--line);background:var(--white);';
    div.innerHTML = `
        <div class="panel-body" style="display:flex;align-items:center;gap:12px;padding:12px 14px;">
            <div class="priority-badge" style="width:36px;height:36px;border-radius:50%;background:var(--coffee-900);color:var(--gold-500);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;flex:none;">1</div>
            <div style="flex:1;min-width:0;">
                <div class="priority-label" style="font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--terracotta-600);">First Choice</div>
                <div style="font-weight:700;font-size:13px;color:var(--coffee-900);line-height:1.3;">${name}</div>
                <div style="font-size:11.5px;color:var(--ink-soft);">${code} · ${campus}</div>
            </div>
            <button type="button" class="remove-btn btn-icon danger" data-id="${progId}" title="Remove" style="flex:none;">×</button>
        </div>
    `;
    div.querySelector('.remove-btn').addEventListener('click', ()=> handleRemove(progId));
    return div;
}

function handleAdd(progId){
    const availItem = availableList.querySelector(`.avail-item[data-id="${progId}"]`);
    if(!availItem) return;
    if(selectedList.querySelectorAll('.selected-item').length >= 3){
        alert('You can select up to 3 programmes.');
        return;
    }
    const name = availItem.querySelector('div div')?.textContent?.trim() || '';
    const meta = availItem.querySelector('div div:nth-child(2)')?.textContent?.trim() || '';
    const code = meta.split('·')[0]?.trim() || availItem.dataset.id;
    const campus = meta.split('·')[3]?.trim() || '';
    availItem.style.display = 'none';
    const sel = createSelectedItem(progId, name, code, campus);
    selectedList.appendChild(sel);
    refreshPriorities();
}

function handleRemove(progId){
    const selItem = selectedList.querySelector(`.selected-item[data-id="${progId}"]`);
    if(selItem) selItem.remove();
    const availItem = availableList.querySelector(`.avail-item[data-id="${progId}"]`);
    if(availItem) availItem.style.display = 'flex';
    refreshPriorities();
}

document.querySelectorAll('.add-btn').forEach(btn=>{
    btn.addEventListener('click', ()=> handleAdd(btn.dataset.id));
});
selectedList.querySelectorAll('.remove-btn').forEach(btn=>{
    btn.addEventListener('click', ()=> handleRemove(btn.dataset.id));
});

searchEl?.addEventListener('input', ()=>{
    const q = searchEl.value.toLowerCase().trim();
    availableList.querySelectorAll('.avail-item').forEach(row=>{
        const isSelected = selectedList.querySelector(`.selected-item[data-id="${row.dataset.id}"]`);
        if(isSelected){ row.style.display = 'none'; return; }
        const match = !q || row.dataset.name.includes(q);
        row.style.display = match ? 'flex' : 'none';
    });
    updateCounts();
});

updateCounts();
refreshPriorities();

// Inline onsubmit now handles validation + confirmModal; keep listener as fallback no-op
// (removed duplicate alerts to avoid double modal/alert on submit)
</script>
@endsection
