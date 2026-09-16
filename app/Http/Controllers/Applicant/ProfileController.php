<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\District;
use App\Models\Region;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user      = auth()->user();
        $applicant = $user->applicant;

        return view('applicant.profile.show', [
            'user'      => $user,
            'applicant' => $applicant,
            'countries' => Country::where('is_active', true)->orderBy('name')->get(),
            'regions'   => Region::where('is_active', true)->orderBy('name')->get(),
            'districts' => District::orderBy('name')->get(),
            'wards'     => Ward::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $user      = auth()->user();
        $applicant = $user->applicant;

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => ['required', 'string', 'max:30'],
        ]);

        $user->update($validated);

        if ($applicant) {
            $request->validate([
                'nida_number' => ['nullable', 'string', 'max:40'],
            ]);

            $applicant->update([
                'nida_number' => $request->input('nida_number'),
                'phone'       => $validated['phone'],
                'email'       => $validated['email'],
            ]);
        }

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return back()->with('success', 'Password changed.');
    }
}