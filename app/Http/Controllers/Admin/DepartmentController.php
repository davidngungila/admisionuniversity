<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index() {
        return view('admin.departments.index', ['departments' => Department::with('faculty')->latest()->paginate(15)]);
    }
    public function create() {
        return view('admin.departments.create', ['faculties' => Faculty::orderBy('name')->get()]);
    }
    public function store(Request $r) {
        $d = $r->validate(['faculty_id'=>['required','exists:faculties,id'],'name'=>['required','string','max:191'],'code'=>['nullable','string','max:20'],'is_active'=>['sometimes','boolean']]);
        $d['is_active']=$r->boolean('is_active', true);
        Department::create($d);
        return redirect()->route('admin.departments.index')->with('success','Department created.');
    }
    public function edit(Department $department) {
        return view('admin.departments.edit', ['department'=>$department,'faculties'=>Faculty::orderBy('name')->get()]);
    }
    public function update(Request $r, Department $department) {
        $d = $r->validate(['faculty_id'=>['required','exists:faculties,id'],'name'=>['required','string','max:191'],'code'=>['nullable','string','max:20'],'is_active'=>['sometimes','boolean']]);
        $d['is_active']=$r->boolean('is_active');
        $department->update($d);
        return redirect()->route('admin.departments.index')->with('success','Updated.');
    }
    public function destroy(Department $department) {
        if ($department->programmes()->exists()) return back()->with('error','Cannot delete — programmes linked.');
        $department->delete();
        return back()->with('success','Deleted.');
    }
}