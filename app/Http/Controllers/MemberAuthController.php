<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class MemberAuthController extends Controller
{
    public function showLogin()
    {
        return view('member.login');
    }

    public function login(Request $request)
    {
        return redirect()
            ->route('member.login')
            ->withErrors([
                'email' => 'Login member hanya tersedia dengan Google. Silakan klik tombol Login dengan Google.',
            ]);
    }

    public function redirectToGoogle()
    {
        try {
            return $this->googleDriver()->redirect();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('member.login')
                ->withErrors([
                    'email' => $exception->getMessage(),
                ]);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = $this->googleDriver()->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('member.login')
                ->withErrors([
                    'email' => $exception->getMessage() ?: 'Login Google gagal diproses. Silakan coba lagi.',
                ]);
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()
                ->route('member.login')
                ->withErrors([
                    'email' => 'Akun Google Anda tidak memiliki email yang dapat digunakan.',
                ]);
        }

        $member = Member::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        $payload = [
            'name' => $googleUser->getName() ?: Str::before($email, '@'),
            'email' => $email,
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ];

        if (! $member) {
            $payload['password'] = Hash::make(Str::random(40));
            $payload['payment_proof_path'] = '';
            $payload['is_active'] = false;
            $payload['paid_at'] = null;
            $member = Member::query()->create($payload);
        } else {
            $member->fill($payload)->save();
        }

        $request->session()->put('member_id', $member->id);
        $request->session()->regenerate();

        if (! $member->is_active) {
            return redirect()
                ->route('member.payment')
                ->with('status', 'Login Google berhasil. Lanjutkan dengan upload bukti pembayaran agar akun bisa diaktifkan admin.');
        }

        return redirect()->intended(route('ebook.home'));
    }

    public function showPayment(Request $request)
    {
        $member = $this->resolveSessionMember($request);

        if (! $member) {
            return redirect()->route('member.login');
        }

        if ($member->is_active) {
            return redirect()->route('ebook.home');
        }

        return view('member.payment', [
            'member' => $member,
        ]);
    }

    public function submitPayment(Request $request)
    {
        $member = $this->resolveSessionMember($request);

        if (! $member) {
            return redirect()->route('member.login');
        }

        if ($member->is_active) {
            return redirect()->route('ebook.home');
        }

        $data = $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $proofFile = $data['payment_proof'];
        $fileName = 'proof-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $proofFile->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('uploads/payment-proofs', $proofFile, $fileName);

        $member->forceFill([
            'payment_proof_path' => 'storage-public/uploads/payment-proofs/' . $fileName,
            'is_active' => false,
            'paid_at' => null,
        ])->save();

        return redirect()
            ->route('member.payment')
            ->with('status', 'Bukti pembayaran berhasil dikirim. Tunggu verifikasi admin untuk akses penuh e-book.');
    }

    public function showRegister()
    {
        return view('member.register');
    }

    public function showForgotPassword()
    {
        return view('member.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::broker('members')->sendResetLink([
            'email' => $request->input('email'),
        ]);

        return back()->with('status', 'Jika email terdaftar, link reset password telah dikirim.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('member.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker('members')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Member $member, string $password) {
                $member->forceFill([
                    'password' => $password,
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('member.login')->with('status', 'Password berhasil direset. Silakan login.');
        }

        return back()->withErrors([
            'email' => 'Link reset tidak valid atau sudah kedaluwarsa.',
        ])->withInput($request->only('email'));
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

        $fileName = 'proof-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $proofFile->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('uploads/payment-proofs', $proofFile, $fileName);

        Member::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'payment_proof_path' => 'storage-public/uploads/payment-proofs/' . $fileName,
            'is_active' => false,
            'paid_at' => null,
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

    private function googleDriver()
    {
        $socialiteClass = 'Laravel\\Socialite\\Facades\\Socialite';

        if (! class_exists($socialiteClass)) {
            throw new RuntimeException('Google Login belum aktif: paket laravel/socialite belum terpasang di server.');
        }

        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            throw new RuntimeException('Google Login belum aktif: GOOGLE_CLIENT_ID/GOOGLE_CLIENT_SECRET belum diatur di .env.');
        }

        return $socialiteClass::driver('google')
            ->redirectUrl(route('member.google.callback'))
            ->scopes(['openid', 'profile', 'email']);
    }

    private function resolveSessionMember(Request $request): ?Member
    {
        $memberId = (int) $request->session()->get('member_id', 0);

        if ($memberId <= 0) {
            return null;
        }

        return Member::query()->find($memberId);
    }
}
