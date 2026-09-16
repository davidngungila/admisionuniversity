<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index() { return view('admin.campuses.index', ['campuses' => Campus::latest()->paginate(15)]); }
    public function create() { return view('admin.campuses.create'); }
    public function store(Request $r) {
        $d = $r->validate(['name' => ['required','string','max:191'],'code'=>['nullable','string','max:20'],'location'=>['nullable','string','max:191'],'is_active'=>['sometimes','boolean']]);
        $d['is_active'] = $r->boolean('is_active', true);
        Campus::create($d);
        return redirect()->route('admin.campuses.index')->with('success','Campus created.');
    }
    public function edit(Campus $campus) { return view('admin.campuses.edit', compact('campus')); }
    public function update(Request $r, Campus $campus) {
        $d = $r->validate(['name'=>['required','string','max:191'],'code'=>['nullable','string','max:20'],'location'=>['nullable','string','max:191'],'is_active'=>['sometimes','boolean']]);
        $d['is_active'] = $r->boolean('is_active');
        $campus->update($d);
        return redirect()->route('admin.campuses.index')->with('success','Campus updated.');
    }
    public function destroy(Campus $campus) {
        if ($campus->programmes()->exists()) return back()->with('error','Cannot delete — programmes linked.');
        $campus->delete();
        return back()->with('success','Deleted.');
    }
}