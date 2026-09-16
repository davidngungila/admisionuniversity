<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index() { return view('admin.faculties.index', ['faculties' => Faculty::withCount('departments')->latest()->paginate(15)]); }
    public function create() { return view('admin.faculties.create'); }
    public function store(Request $r) {
        $d = $r->validate(['name'=>['required','string','max:191'],'code'=>['nullable','string','max:20'],'description'=>['nullable','string'],'is_active'=>['sometimes','boolean']]);
        $d['is_active']=$r->boolean('is_active', true);
        Faculty::create($d);
        return redirect()->route('admin.faculties.index')->with('success','Faculty created.');
    }
    public function edit(Faculty $faculty) { return view('admin.faculties.edit', compact('faculty')); }
    public function update(Request $r, Faculty $faculty) {
        $d = $r->validate(['name'=>['required','string','max:191'],'code'=>['nullable','string','max:20'],'description'=>['nullable','string'],'is_active'=>['sometimes','boolean']]);
        $d['is_active']=$r->boolean('is_active');
        $faculty->update($d);
        return redirect()->route('admin.faculties.index')->with('success','Updated.');
    }
    public function destroy(Faculty $faculty) {
        if ($faculty->departments()->exists()) return back()->with('error','Cannot delete — departments linked.');
        $faculty->delete();
        return back()->with('success','Deleted.');
    }
}