<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        try {
            return $this->googleDriver()->redirect();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.login')
                ->with('error', $exception->getMessage());
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = $this->googleDriver()->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.login')
                ->with('error', $exception->getMessage() ?: 'Login Google gagal diproses. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Akun Google Anda tidak memiliki email yang dapat digunakan.');
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        $payload = [
            'name' => $googleUser->getName() ?: Str::before($email, '@'),
            'email' => $email,
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ];

        if (! $user) {
            $payload['password'] = Hash::make(Str::random(40));
            $user = User::query()->create($payload);
        } else {
            $user->fill($payload)->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
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
            ->scopes(['openid', 'profile', 'email']);
    }
}