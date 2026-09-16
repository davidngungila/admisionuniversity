<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportOfficer;
use Illuminate\Http\Request;

class SupportOfficerController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.support-officers.index', [
            'officers' => SupportOfficer::orderBy('order_index')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.support-officers.form', ['officer' => new SupportOfficer()]);
    }

    public function store(Request $request)
    {
        SupportOfficer::create($this->validated($request));

        return redirect()->route('admin.support-officers.index')->with('success', 'Support officer added.');
    }

    public function edit(SupportOfficer $officer)
    {
        return view('admin.support-officers.form', compact('officer'));
    }

    public function update(Request $request, SupportOfficer $officer)
    {
        $officer->update($this->validated($request));

        return back()->with('success', 'Support officer updated.');
    }

    public function destroy(SupportOfficer $officer)
    {
        $officer->delete();

        return back()->with('success', 'Support officer deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:191'],
            'phone'       => ['required', 'string', 'max:30'],
            'group_name'  => ['nullable', 'string', 'max:191'],
            'designation' => ['nullable', 'string', 'max:191'],
            'is_online'   => ['nullable', 'boolean'],
            'order_index' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active'   => ['nullable', 'boolean'],
        ], [], [
            'group_name' => 'group / category',
        ]);
    }
}