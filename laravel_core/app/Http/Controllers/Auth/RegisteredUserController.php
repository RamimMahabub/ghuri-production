<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Auto-verifies email when MAIL_MAILER is 'log', 'array', or 'null'
     * (no real mail server — typical on Vercel or local dev).
     * On production with a real mailer the normal email-verify flow runs.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'    => ['required', 'string', 'max:20', 'unique:'.User::class.',phone'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'string', 'in:customer,property_owner'],
        ]);

        // Auto-verify when no real mail driver is configured
        $autoVerify = in_array(config('mail.default'), ['log', 'array', 'null']);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'role'              => $request->role,
            'password'          => Hash::make($request->password),
            'email_verified_at' => $autoVerify ? now() : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Skip verification notice when auto-verified — go straight to dashboard
        if ($autoVerify) {
            return redirect()->intended(route($user->getDashboardRoute(), absolute: false));
        }

        return redirect(route('verification.notice', absolute: false));
    }
}
