<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    private const ALLOWED_DOMAIN = 'ust.edu.ph';

    public function redirect(): RedirectResponse
    {
        session()->forget('google_link_intent');

        return Socialite::driver('google')
            ->with(['hd' => self::ALLOWED_DOMAIN])
            ->redirect();
    }

    public function linkRedirect(): RedirectResponse
    {
        session(['google_link_intent' => true]);

        return Socialite::driver('google')
            ->with(['hd' => self::ALLOWED_DOMAIN])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $email = strtolower($googleUser->getEmail());

        if (! str_ends_with($email, '@'.self::ALLOWED_DOMAIN)) {
            session()->forget('google_link_intent');

            return redirect()->route('login')->withErrors([
                'email' => 'Only @'.self::ALLOWED_DOMAIN.' Google accounts are allowed to sign in.',
            ]);
        }

        // Linking flow: user is already logged in and deliberately chose to link.
        if (session()->pull('google_link_intent') && Auth::check()) {
            $currentUser = Auth::user();

            if (strtolower($currentUser->email) !== $email) {
                return redirect()->route('profile.edit')->with('status', 'google-link-mismatch');
            }

            try {
                $currentUser->update(['google_id' => $googleUser->getId()]);
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect()->route('profile.edit')->with('status', 'google-link-conflict');
            }

            return redirect()->route('profile.edit')->with('status', 'google-linked');
        }

        // Normal login flow.
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(Str::random(32)),
                'role' => 'staff',
                'is_active' => false,
                'can_manage_assets' => false,
                'email_verified_at' => now(),
            ]);

            return redirect()->route('login')->with('status',
                'Your account has been created and is pending approval from your system administrator.'
            );
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'email' => 'This account is inactive. Please contact your system administrator to activate it.',
            ]);
        }

        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}