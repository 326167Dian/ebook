<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PharmacyLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PharmacyLogoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'logo_upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
        ]);

        $logoFile = $request->file('logo_upload');
        $fileName = 'apotek-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $logoFile->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('pharmacy-logos', $logoFile, $fileName);

        PharmacyLogo::create([
            'name' => $data['name'],
            'logo_path' => 'storage-public/pharmacy-logos/' . $fileName,
            'website_url' => $data['website_url'] ?? null,
            'sort_order' => (int) (PharmacyLogo::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('admin.footer.edit')->with('status', 'Logo apotek berhasil ditambahkan.');
    }

    public function update(Request $request, PharmacyLogo $pharmacyLogo)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'logo_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
        ]);

        $logoPath = $pharmacyLogo->logo_path;

        if ($request->hasFile('logo_upload')) {
            $logoFile = $request->file('logo_upload');
            $fileName = 'apotek-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . $logoFile->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('pharmacy-logos', $logoFile, $fileName);

            $this->deleteLogoFile($pharmacyLogo->logo_path);
            $logoPath = 'storage-public/pharmacy-logos/' . $fileName;
        }

        $pharmacyLogo->update([
            'name' => $data['name'],
            'website_url' => $data['website_url'] ?? null,
            'logo_path' => $logoPath,
        ]);

        return redirect()->route('admin.footer.edit')->with('status', 'Logo apotek berhasil diperbarui.');
    }

    public function destroy(PharmacyLogo $pharmacyLogo)
    {
        $this->deleteLogoFile($pharmacyLogo->logo_path);
        $pharmacyLogo->delete();

        return redirect()->route('admin.footer.edit')->with('status', 'Logo apotek berhasil dihapus.');
    }

    private function deleteLogoFile(?string $path): void
    {
        $path = (string) $path;
        $prefix = 'storage-public/';

        if ($path === '' || !str_starts_with($path, $prefix)) {
            return;
        }

        $relativePath = substr($path, strlen($prefix));

        if ($relativePath !== '' && Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
