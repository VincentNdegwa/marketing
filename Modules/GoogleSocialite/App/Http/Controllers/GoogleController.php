<?php

namespace Modules\GoogleSocialite\App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\RegisteredUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        try {
            return Socialite::driver('google')
                ->stateless()
                ->with([
                    'access_type' => 'offline',
                    'prompt' => 'select_account consent'
                ])
                ->scopes([
                    'email',
                    'profile',
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile'
                ])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to connect to Google. Please try again.');
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            if (!$googleUser) {
                return redirect()->route('login')->with('error', 'Unable to retrieve user information from Google.');
            }

            
            $existingUser = User::whereHas('tokens', function ($query) use ($googleUser) {
                $query->where('google_id', $googleUser->id);
            })->first();
            
            if ($existingUser) {
                Auth::login($existingUser);
                return redirect()->route('dashboard')->with('success', 'Logged in successfully.');
            }

            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(16)),
                'avatar' => $googleUser->avatar,
                'email_verified_at' => now(),
                'type' => 'admin',
            ]);
            $newUser->tokens()->updateOrCreate(
                ['user_id' => $newUser->id],
                [
                    'google_id' => $googleUser->id,
                    'google_token' => isset($googleUser->token) ? $googleUser->token : null,
                    'google_refresh_token' => isset($googleUser->refreshToken) ? $googleUser->refreshToken : null,
                ]
            );
            event(new RegisteredUser($newUser));
            Auth::login($newUser);
            return redirect()->route('dashboard')->with('success', 'Logged in successfully.');

        } catch (\Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}
