<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResellerAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($this->resolveSessionReseller($request)) {
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $reseller = Reseller::query()->where('username', $credentials['username'])->first();

        if (! $reseller || ! Hash::check($credentials['password'], $reseller->password)) {
            return back()->withErrors([
                'username' => 'Username atau password tidak sesuai.',
            ])->onlyInput('username');
        }

        $request->session()->put('reseller_id', $reseller->id_reseller);
        $request->session()->regenerate();

        if (! $reseller->is_active) {
            return redirect()->route('reseller.pending');
        }

        return redirect()->route('reseller.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('reseller_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('reseller.login');
    }

    public function showRegister()
    {
        return view('reseller.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:60', 'unique:reseller,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nm_reseller' => ['required', 'string', 'max:120'],
            'telp' => ['required', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'bank' => ['required', 'string', 'max:60'],
            'rekening' => ['required', 'string', 'max:50'],
        ]);

        Reseller::query()->create([
            'username' => $data['username'],
            'password' => $data['password'],
            'nm_reseller' => $data['nm_reseller'],
            'telp' => $data['telp'],
            'alamat' => $data['alamat'] ?? null,
            'bank' => $data['bank'],
            'rekening' => $data['rekening'],
            'is_active' => false,
        ]);

        return redirect()
            ->route('reseller.login')
            ->with('status', 'Registrasi berhasil. Akun Anda akan aktif setelah disetujui admin.');
    }

    public function showPending(Request $request)
    {
        $reseller = $this->resolveSessionReseller($request);

        if (! $reseller) {
            return redirect()->route('reseller.login');
        }

        if ($reseller->is_active) {
            return redirect()->route('reseller.dashboard');
        }

        return view('reseller.pending', [
            'reseller' => $reseller,
        ]);
    }

    public function dashboard(Request $request)
    {
        $reseller = $request->attributes->get('reseller');

        $members = Member::query()
            ->where('id_reseller', $reseller->id_reseller)
            ->latest()
            ->get(['id', 'name', 'email']);

        return view('reseller.dashboard', [
            'reseller' => $reseller,
            'members' => $members,
        ]);
    }

    private function resolveSessionReseller(Request $request): ?Reseller
    {
        $resellerId = (int) $request->session()->get('reseller_id', 0);

        if ($resellerId <= 0) {
            return null;
        }

        return Reseller::query()->find($resellerId);
    }
}
