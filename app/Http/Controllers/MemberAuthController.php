<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberAuthController extends Controller
{
    public function showLogin()
    {
        return view('member.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $member = Member::query()->where('email', $credentials['email'])->first();

        if (!$member || !Hash::check($credentials['password'], $member->password)) {
            return back()->withErrors([
                'email' => 'Email atau password tidak sesuai.',
            ])->onlyInput('email');
        }

        if (!$member->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda masih menunggu verifikasi pembayaran oleh admin.',
            ])->onlyInput('email');
        }

        $request->session()->put('member_id', $member->id);
        $request->session()->regenerate();

        return redirect()->intended(route('ebook.home'));
    }

    public function showRegister()
    {
        return view('member.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:members,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $proofFile = $request->file('payment_proof');
        $directory = public_path('uploads/payment-proofs');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $fileName = 'proof-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $proofFile->getClientOriginalExtension();
        $proofFile->move($directory, $fileName);

        Member::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'payment_proof_path' => 'uploads/payment-proofs/' . $fileName,
            'is_active' => false,
            'paid_at' => now(),
        ]);

        return redirect()->route('member.login')->with('status', 'Registrasi berhasil. Akun Anda akan aktif setelah bukti transfer diverifikasi admin.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('member_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }
}
