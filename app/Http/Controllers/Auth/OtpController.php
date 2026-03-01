<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    /**
     * Show OTP verification form
     */
    public function showVerifyForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Sesi OTP tidak valid. Silakan login ulang.');
        }
        
        return view('auth.verify');
    }

    /**
     * Verify OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ], [
            'otp.required' => 'Kode OTP harus diisi',
            'otp.digits' => 'Kode OTP harus 6 digit'
        ]);

        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi OTP tidak valid. Silakan login ulang.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        if ($user->otp === $request->otp) {
            $user->update(['otp' => null]);
            
            session()->forget('otp_user_id');
            
            Auth::login($user);
            
            return redirect()->route('dashboard')
                ->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
        } else {
            return back()
                ->with('error', 'Kode OTP tidak valid. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Resend OTP code
     */
    public function resend()
    {
        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi OTP tidak valid. Silakan login ulang.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        $otp = rand(100000, 999999);
        
        $user->update(['otp' => $otp]);
        
        $user->notify(new \App\Notifications\OtpNotification($otp));
        
        return back()
            ->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}