<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * Failed sign-in attempts allowed per email+IP before the pair is locked out.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout window in seconds. Also the decay time of the attempt counter.
     */
    private const DECAY_SECONDS = 60;

    public function login(Request $request)
    {
        if (! $request->isMethod('post')) {
            return view('auth.login');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "बढी प्रयास गरिएको छ। {$seconds} सेकेन्ड पछि प्रयास गर्नुहोस्। "
                    ."Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            return redirect()->back()
                ->with('error', 'Credentials do not match our records.')
                ->withInput();
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        // Land somewhere this account is actually allowed to open. The dashboard
        // is gated on dashboard.view, so an account with no permissions would
        // otherwise be redirected straight into a 403.
        if (! $request->user()->hasPermission('dashboard.view')) {
            return redirect()->route('no-access');
        }

        return redirect()->intended(route('accounting.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Landing page for a signed-in account whose role holds no permissions.
     *
     * Without this, such a user is redirected to the dashboard by login() and
     * receives a bare 403 with no way forward.
     */
    public function noAccess(Request $request)
    {
        $user = $request->user();

        return view('auth.no_access', [
            'roleName' => $user->role?->name,
            'roleDescription' => $user->role?->description,
        ]);
    }

    /**
     * Show the reset-link request form and send the link when it is submitted.
     *
     * The response is deliberately identical whether or not the email belongs
     * to an account, so the endpoint cannot be used to enumerate users.
     */
    public function forgot_password(Request $request)
    {
        if (! $request->isMethod('post')) {
            return view('auth.forgot_password');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // Reply identically for a known and an unknown address so the endpoint
        // cannot be used to enumerate registered users. Throttling is the only
        // outcome that warrants a distinct message.
        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->with('error', __($status));
        }

        return redirect()->route('login')->with('success', __('passwords.sent'));
    }

    /**
     * Show the form where a user sets a new password from a reset token.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Consume the reset token and store the new password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // The password attribute is cast to "hashed", so assigning the
                // plain value here stores the hash exactly once.
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    private function throttleKey(Request $request): string
    {
        return 'login|'.Str::transliterate(
            Str::lower($request->input('email')).'|'.$request->ip()
        );
    }
}
