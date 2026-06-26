<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Institution;
use App\Models\Organization;
use App\Models\Student;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account registration is pending approval by the System Administrator.',
                ]);
            }

            if ($user->status === 'deactivated') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route($user->getDashboardRoute()));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }
        $roles = Role::whereNotIn('name', ['admin'])->get();
        $institutions = Institution::where('status', 'active')->orderBy('name')->get();
        return view('auth.register', compact('roles', 'institutions'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role_id'   => ['required', 'exists:roles,id'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Prevent registering as admin
        $role = Role::findOrFail($request->role_id);
        if ($role->name === 'admin') {
            return back()->withErrors(['role_id' => 'Invalid role selected.']);
        }

        // Role-specific validation
        if ($role->name === 'student') {
            $request->validate([
                'institution_id'      => ['nullable', 'exists:institutions,id'],
                'registration_number' => ['nullable', 'string', 'max:100'],
            ]);
        } elseif ($role->name === 'institution') {
            $request->validate([
                'inst_registration_number' => ['required', 'string', 'max:100', 'unique:institutions,registration_number'],
                'location'                 => ['required', 'string', 'max:255'],
            ]);
        } elseif ($role->name === 'organization') {
            $request->validate([
                'contact_person' => ['required', 'string', 'max:255'],
                'description'    => ['required', 'string', 'max:1000'],
            ]);
        }

        // Determine user status
        $userStatus = ($role->name === 'student') ? 'active' : 'pending';

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'status'   => $userStatus,
        ]);

        // Create Profile based on role
        if ($role->name === 'student') {
            Student::create([
                'user_id'             => $user->id,
                'institution_id'      => $request->institution_id,
                'registration_number' => $request->registration_number,
                'phone'               => $request->phone,
            ]);

            AuditLog::log($user->id, 'register_student', 'Registered student account for ' . $user->name);
            Auth::login($user);

            return redirect()->route($user->getDashboardRoute())
                ->with('success', 'Welcome to SkillConnect! Your account has been created successfully.');
        } elseif ($role->name === 'institution') {
            Institution::create([
                'user_id'             => $user->id,
                'name'                => $user->name,
                'registration_number' => $request->inst_registration_number,
                'location'            => $request->location,
                'phone'               => $request->phone,
                'status'              => 'pending',
            ]);

            AuditLog::log($user->id, 'register_institution_pending', 'Registered pending institution account for ' . $user->name);

            return redirect()->route('login')
                ->with('success', 'Your registration has been submitted successfully and is pending approval by the System Administrator.');
        } elseif ($role->name === 'organization') {
            Organization::create([
                'user_id'        => $user->id,
                'name'           => $user->name,
                'contact_person' => $request->contact_person,
                'phone'          => $request->phone,
                'description'    => $request->description,
                'status'         => 'pending',
            ]);

            AuditLog::log($user->id, 'register_organization_pending', 'Registered pending organization account for ' . $user->name);

            return redirect()->route('login')
                ->with('success', 'Your registration has been submitted successfully and is pending approval by the System Administrator.');
        }

        return redirect()->route('login');
    }

    // ─── Forgot Password ──────────────────────────────────────────────────────

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::ResetLinkSent
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('success', 'Password reset successfully! Please login.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
