<?php

namespace Modules\FbSocialite\App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;


class FaceBookController extends Controller
{
    public function loginUsingFacebook(Request $request)
    {
        try {
            return Socialite::driver('facebook')
                ->stateless()
                ->with([
                    'display' => 'popup',
                    'auth_type' => 'rerequest'
                ])
                ->scopes(['email', 'public_profile'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Facebook login error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to connect to Facebook. Please try again.');
        }
    }

    public function callbackFromFacebook(Request $request)
    {
        try {
            $user = Socialite::driver('facebook')
                ->stateless()
                ->user();
            if(!$user) {
                return redirect()->route('login')->with('error', 'Unable to retrieve user information from Facebook.');
            }
            $existingFbUser = User::where('facebook_id', $user->id)->first();
            if ($existingFbUser) {
                Auth::login($existingFbUser);
                return redirect()->route('dashboard')->with('success', 'Logged in successfully.');

            }else{
                $user = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => bcrypt(Str::random(16)),
                    'avatar' => $user->avatar,
                    'facebook_id' => $user->id,
                    'email_verified_at' => now(),
                    'type' => 'admin',
                ]);
                event(new Registered($user));
                Auth::login($user);
                return redirect()->route('dashboard')->with('success', 'Logged in successfully.');
            }


            dd($user);
        } catch (\Exception $e) {
            Log::error('Facebook callback error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Facebook login failed: ' . $e->getMessage());
        }
    }
}
