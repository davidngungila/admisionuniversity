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
        $request->validate([
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            // Back-compat: the old form posted `email` instead of `login`.
            'email'    => ['nullable', 'string'],
        ]);

        $login = trim($request->input('login') ?? $request->input('email') ?? '');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $attempts = [];
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $attempts[] = ['email' => $login, 'password' => $password];
        } else {
            // Index number / username / passport / chosen username is stored in users.name
            // (Tanzanian: index, International: "First Surname", Postdoc: chosen username).
            // Also try the applicants tables for index / passport / username.
            $attempts[] = ['name' => $login, 'password' => $password];
            $attempts[] = ['email' => $login, 'password' => $password];
        }

        foreach ($attempts as $credentials) {
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                $user = Auth::user();
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('applicant.dashboard');
            }
        }

        // Fallback: look up applicant by exam_index_number / passport_number / username and try the linked user.
        $applicant = \App\Models\Applicant::where('exam_index_number', $login)
            ->orWhere('passport_number', $login)
            ->orWhere('username', $login)
            ->first();
        if ($applicant && $applicant->user) {
            if (Auth::attempt(['email' => $applicant->user->email, 'password' => $password], $remember)) {
                $request->session()->regenerate();
                $user = Auth::user();
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('applicant.dashboard');
            }
        }

        return back()->withErrors(['login' => 'The provided credentials do not match our records.'])
            ->onlyInput('login');
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
                'entry_type'            => ['required', 'string', 'in:direct,equivalent'],
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
                'reg_username'            => null,
                'reg_phd_graduation_year' => null,
                'reg_first_name'          => null,
                'reg_surname'             => null,
                'reg_scholarship_category'=> null,
            ];
            $successMsg = 'Account created. Your index number '.$validated['index_number'].' is your username. Choose an admission window below to start'.($level ? ' for '.$level->name : '').'.';
        } elseif ($category === 'international') {
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
                'reg_applicant_category'  => 'international',
                'reg_first_name'          => $validated['first_name'],
                'reg_surname'             => $validated['surname'],
                'reg_passport_number'     => $validated['passport_number'],
                'reg_username'            => null,
                'reg_phd_graduation_year' => null,
                'reg_scholarship_category'=> $validated['scholarship_category'],
                'reg_application_type'    => $level?->name ?? $validated['application_type'],
                'reg_index_number'        => null,
                'reg_entry_type'          => null,
            ];
            $successMsg = 'Account created. Your passport number '.$validated['passport_number'].' is your username. Choose an admission window below to start'.($level ? ' for '.$level->name : '').'.';
        } else {
            // Post-Doctoral
            $validated = $request->validate([
                'applicant_category'    => ['required','string','in:tanzanian,international,postdoctoral'],
                'first_name'            => ['required','string','min:2','max:255'],
                'surname'               => ['required','string','min:2','max:255'],
                'username'              => ['required','string','min:3','max:40','unique:applicants,username','unique:users,name'],
                'phd_graduation_year'   => ['required','integer','min:2021','max:'.(date('Y')+1)],
                'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone'                 => ['required', 'string', 'regex:/^\+?[0-9]{9,15}$/', 'max:30'],
                'scholarship_category'  => ['required', 'string', 'max:60'],
                'password'              => ['required', 'confirmed', Password::defaults()],
            ]);
            $displayName = trim($validated['username']);
            $sessionData = [
                'reg_applicant_category'  => 'postdoctoral',
                'reg_first_name'          => $validated['first_name'],
                'reg_surname'             => $validated['surname'],
                'reg_username'            => $validated['username'],
                'reg_phd_graduation_year' => $validated['phd_graduation_year'],
                'reg_scholarship_category'=> $validated['scholarship_category'],
                'reg_application_type'    => 'PhD',
                'reg_passport_number'     => null,
                'reg_index_number'        => null,
                'reg_entry_type'          => null,
            ];
            // Postdoc level is always PhD
            $phdLevel = \App\Models\AdmissionLevel::where('code','PHD')->first();
            $sessionData['intended_level_id'] = $phdLevel?->id;
            $sessionData['intended_level_name'] = $phdLevel?->name ?? 'PhD';
            $successMsg = 'Account created. Your username '.$validated['username'].' is ready. Choose an admission window below to start for PhD.';
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