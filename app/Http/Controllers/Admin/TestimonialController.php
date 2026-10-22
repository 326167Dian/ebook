<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.testimonials', [
            'testimonials' => $testimonials,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Testimonial::create([
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'content' => $data['content'],
            'rating' => (int) $data['rating'],
            'photo_path' => $request->hasFile('photo_upload') ? $this->storePhoto($request) : null,
            'is_visible' => $request->boolean('is_visible'),
            'sort_order' => (int) (Testimonial::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimoni berhasil ditambahkan.');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validateData($request);

        $photoPath = $testimonial->photo_path;

        if ($request->boolean('remove_photo')) {
            $this->deletePhotoFile($photoPath);
            $photoPath = null;
        }

        if ($request->hasFile('photo_upload')) {
            $this->deletePhotoFile($photoPath);
            $photoPath = $this->storePhoto($request);
        }

        $testimonial->update([
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'content' => $data['content'],
            'rating' => (int) $data['rating'],
            'photo_path' => $photoPath,
            'is_visible' => $request->boolean('is_visible'),
            'sort_order' => (int) ($data['sort_order'] ?? $testimonial->sort_order),
        ]);

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimoni berhasil diperbarui.');
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_visible' => !$testimonial->is_visible]);

        $message = $testimonial->is_visible ? 'Testimoni ditampilkan di landing page.' : 'Testimoni disembunyikan dari landing page.';

        return redirect()->route('admin.testimonials.index')->with('status', $message);
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->deletePhotoFile($testimonial->photo_path);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimoni berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:1000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'photo_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);
    }

    private function storePhoto(Request $request): string
    {
        $file = $request->file('photo_upload');
        $fileName = 'testimoni-' . now()->format('YmdHis') . '-' . Str::random(6) . '.' . strtolower($file->getClientOriginalExtension());
        Storage::disk('public')->putFileAs('testimonials', $file, $fileName);

        return 'storage-public/testimonials/' . $fileName;
    }

    private function deletePhotoFile(?string $path): void
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
