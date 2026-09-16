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
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'               => ['required', 'string', 'max:30'],
            'password'            => ['required', 'confirmed', Password::defaults()],
            'citizenship_id'      => ['nullable', 'exists:countries,id'],
            'intended_level_id'   => ['required', 'exists:admission_levels,id'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
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

        // Remember what the applicant wants to apply for — used to highlight relevant windows on dashboard
        if (! empty($validated['intended_level_id'])) {
            $level = \App\Models\AdmissionLevel::find($validated['intended_level_id']);
            session(['intended_level_id' => $level?->id, 'intended_level_name' => $level?->name]);
        }

        return redirect()->route('applicant.dashboard')->with('success', 'Account created. You selected to apply for '.(session('intended_level_name') ?? 'your chosen programme').'. Choose an admission window below to start.');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}