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
            'levels' => \App\Models\AdmissionLevel::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function register(Request $request)
    {
        $category = $request->input('applicant_category', 'tanzanian');
        if (! in_array($category, ['tanzanian','international','postdoctoral','post_doctoral'], true)) {
            $category = 'tanzanian';
        }
        // Normalize post_doctoral alias
        if ($category === 'post_doctoral') $category = 'postdoctoral';

        if ($category === 'tanzanian') {
            $validated = $request->validate([
                'applicant_category'    => ['required','string','in:tanzanian,international,postdoctoral'],
                'entry_type'            => ['required', 'string', 'max:40'],
                'application_type'      => ['required', 'exists:admission_levels,id'],
                'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'index_number'          => ['required', 'string', 'max:40'],
                'phone'                 => ['required', 'string', 'regex:/^\+?[0-9]{9,15}$/', 'max:30'],
                'password'              => ['required', 'confirmed', Password::defaults()],
            ]);
            $level = \App\Models\AdmissionLevel::find($validated['application_type']);
            $displayName = $validated['index_number'];
            $sessionData = [
                'intended_level_id'       => $level?->id,
                'intended_level_name'     => $level?->name,
                'reg_applicant_category'  => 'tanzanian',
                'reg_index_number'        => $validated['index_number'],
                'reg_entry_type'          => $validated['entry_type'],
                'reg_application_type'    => $level?->name ?? $validated['application_type'],
                'reg_passport_number'     => null,
                'reg_first_name'          => null,
                'reg_surname'             => null,
                'reg_scholarship_category'=> null,
            ];
            $successMsg = 'Account created. Your index number '.$validated['index_number'].' is your username. Choose an admission window below to start'.($level ? ' for '.$level->name : '').'.';
        } else {
            // International / Post-Doctoral share the same form; category kept for display.
            $validated = $request->validate([
                'applicant_category'    => ['required','string','in:tanzanian,international,postdoctoral'],
                'first_name'            => ['required','string','min:2','max:255'],
                'surname'               => ['required','string','min:2','max:255'],
                'passport_number'       => ['required','string','max:40','unique:applicants,passport_number'],
                'scholarship_category'  => ['required', 'string', 'max:60'],
                'application_type'      => ['required', 'exists:admission_levels,id'],
                'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone'                 => ['required', 'string', 'regex:/^\+?[0-9]{9,15}$/', 'max:30'],
                'password'              => ['required', 'confirmed', Password::defaults()],
            ]);
            $level = \App\Models\AdmissionLevel::find($validated['application_type']);
            $displayName = trim($validated['first_name'].' '.$validated['surname']);
            $sessionData = [
                'intended_level_id'       => $level?->id,
                'intended_level_name'     => $level?->name,
                'reg_applicant_category'  => $category,
                'reg_first_name'          => $validated['first_name'],
                'reg_surname'             => $validated['surname'],
                'reg_passport_number'     => $validated['passport_number'],
                'reg_scholarship_category'=> $validated['scholarship_category'],
                'reg_application_type'    => $level?->name ?? $validated['application_type'],
                'reg_index_number'        => null,
                'reg_entry_type'          => null,
            ];
            $successMsg = 'Account created. Your passport number '.$validated['passport_number'].' is your username. Choose an admission window below to start'.($level ? ' for '.$level->name : '').'.';
        }

        $user = DB::transaction(function () use ($validated, $displayName) {
            $user = User::create([
                'name'     => $displayName,
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

        session($sessionData);

        return redirect()->route('applicant.dashboard')->with('success', $successMsg);
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