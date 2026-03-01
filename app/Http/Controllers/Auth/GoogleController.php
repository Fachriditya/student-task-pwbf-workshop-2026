<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth page
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('id_google', $googleUser->getId())->first();
            
            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if ($user) {
                    $user->update([
                        'id_google' => $googleUser->getId()
                    ]);
                } else {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'id_google' => $googleUser->getId(),
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);
                }
            }
            
            $otp = rand(100000, 999999);
            
            $user->update([
                'otp' => $otp
            ]);

            $user->notify(new \App\Notifications\OtpNotification($otp));
            
            session(['otp_user_id' => $user->id]);
            
            return redirect()->route('otp.verify')
                ->with('success', 'OTP telah dikirim ke email Anda: ' . $user->email);
                
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}