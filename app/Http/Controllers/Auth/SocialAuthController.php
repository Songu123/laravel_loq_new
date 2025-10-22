<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        // Handle SSL certificate issues in development
        if (config('curl.verify') === false) {
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false
            ]);
            
            return Socialite::driver('google')
                ->setHttpClient($httpClient)
                ->redirect();
        }
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            // Handle SSL certificate issues in development
            if (config('curl.verify') === false) {
                $httpClient = new \GuzzleHttp\Client([
                    'verify' => false
                ]);
                
                $googleUser = Socialite::driver('google')
                    ->setHttpClient($httpClient)
                    ->user();
            } else {
                $googleUser = Socialite::driver('google')->user();
            }
            
            // Find or create user
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update existing user with Google info
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)), // Random password
                    'role' => 'student', // Default role
                    'email_verified_at' => now(),
                ]);
            }

            // Login user
            Auth::login($user, true);

            // Redirect based on role
            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Google OAuth Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Redirect based on user role
     */
    private function redirectBasedOnRole($user)
    {
        $message = 'Đăng nhập thành công với Google!';
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', $message);
            case 'teacher':
                return redirect()->route('teacher.dashboard')->with('success', $message);
            case 'student':
                return redirect()->route('student.dashboard')->with('success', $message);
            default:
                return redirect()->route('home')->with('success', $message);
        }
    }

    /**
     * Redirect to Facebook OAuth (optional - for future)
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook OAuth callback (optional - for future)
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = User::where('email', $facebookUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);

            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Facebook thất bại. Vui lòng thử lại!');
        }
    }
}
