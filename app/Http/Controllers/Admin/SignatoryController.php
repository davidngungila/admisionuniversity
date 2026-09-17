<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signatory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatoryController extends Controller
{
    public function index()
    {
        return view('admin.signatories.index', ['signatories' => Signatory::orderBy('order_index')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.signatories.form', ['signatory' => new Signatory()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $signatory = Signatory::create($data);
        $this->attachSignature($request, $signatory);

        if ($signatory->is_active) {
            Signatory::where('id', '!=', $signatory->id)->update(['is_active' => false]);
        }

        return redirect()->route('admin.signatories.index')->with('success', 'Signatory added.');
    }

    public function edit(Signatory $signatory)
    {
        return view('admin.signatories.form', compact('signatory'));
    }

    public function update(Request $request, Signatory $signatory)
    {
        $data = $this->validated($request);

        if (! $request->has('signature_image') || ! $request->file('signature_image')) {
            unset($data['signature_image']);
        }

        $signatory->update($data);
        $this->attachSignature($request, $signatory);

        if ($signatory->fresh()->is_active) {
            Signatory::where('id', '!=', $signatory->id)->update(['is_active' => false]);
        }

        return back()->with('success', 'Signatory updated.');
    }

    public function destroy(Signatory $signatory)
    {
        if ($signatory->signature_image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $signatory->signature_image));
        }
        $signatory->delete();

        return back()->with('success', 'Signatory deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:191'],
            'title'          => ['nullable', 'string', 'max:40'],
            'designation'    => ['nullable', 'string', 'max:191'],
            'order_index'    => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active'      => ['nullable', 'boolean'],
        ]);
    }

    protected function attachSignature(Request $request, Signatory $signatory): void
    {
        if (! $request->hasFile('signature_image')) {
            return;
        }

        $path = $request->file('signature_image')->store(Signatory::path, 'public');
        if ($signatory->signature_image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $signatory->signature_image));
        }

        $signatory->update(['signature_image' => 'storage/'.$path]);
    }
}