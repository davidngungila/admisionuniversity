<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Always direct to dashboard (no intended redirect)
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('applicant.dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register', [
            'countries' => Country::where('is_active', true)->orderBy('name')->get(),
            'levels'    => \App\Models\AdmissionLevel::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'               => ['required', 'string', 'regex:/^\+?[0-9]{9,15}$/', 'max:30'],
            'password'            => ['required', 'confirmed', Password::defaults()],
            'citizenship_id'      => ['nullable', 'exists:countries,id'],
            'intended_level_id'   => ['required', 'exists:admission_levels,id'],
            'application_type'    => ['required', 'string', 'max:40'],
            'index_number'        => ['required', 'string', 'max:40'],
            'first_name'          => ['required', 'string', 'min:2', 'max:255'],
        ]);

        // Confirm the applicant against NECTA using index number + first name.
        $necta = app(\App\Services\ResultVerificationService::class)->verify('O-Level', [
            'index_number' => $validated['index_number'],
            'first_name'   => $validated['first_name'],
        ]);

        if (empty($necta['ok']) || empty($necta['full_name'])) {
            return back()->withInput()->withErrors(['first_name' => 'We could not fetch your name from NECTA. Check your Form Four index number and first name, then try again.']);
        }

        $fullName = $necta['full_name'];

        $user = DB::transaction(function () use ($validated, $fullName) {
            $user = User::create([
                'name'     => $fullName,
                'email'    => $validated['email'],
                'phone'    => $validated['phone'],
                'password' => $validated['password'],
                'role'     => 'applicant',
                'is_active' => true,
            ]);

            $applicantRole = Role::where('slug', 'applicant')->first();
            if ($applicantRole) {
                $user->roles()->attach($applicantRole);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        // Remember what the applicant wants to apply for and the NECTA-verified identity.
        // These are used to prefill the applicant record when an application is started.
        $level = \App\Models\AdmissionLevel::find($validated['intended_level_id']);
        session([
            'intended_level_id'       => $level?->id,
            'intended_level_name'     => $level?->name,
            'reg_index_number'        => $validated['index_number'],
            'reg_first_name'          => $validated['first_name'],
            'reg_application_type'    => $validated['application_type'],
            'reg_verified_full_name'  => $fullName,
        ]);

        return redirect()->route('applicant.dashboard')->with('success', 'Account created. You selected to apply for '.(session('intended_level_name') ?? 'your chosen programme').'. Choose an admission window below to start.');
    }

    public function fetchName(Request $request)
    {
        $validated = $request->validate([
            'index_number' => ['required', 'string', 'max:40'],
            'first_name'   => ['required', 'string', 'min:2', 'max:255'],
        ]);

        $result = app(\App\Services\ResultVerificationService::class)->verify('O-Level', $validated);

        if (empty($result['ok'])) {
            return response()->json(['ok' => false, 'error' => $result['error'] ?? 'Could not fetch the candidate name.'], 422);
        }

        return response()->json([
            'ok'         => true,
            'full_name'  => $result['full_name'] ?? '',
            'identifier' => $result['identifier'] ?? '',
            'school_name'=> $result['school_name'] ?? '',
            'exam_year'  => $result['exam_year'] ?? null,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}