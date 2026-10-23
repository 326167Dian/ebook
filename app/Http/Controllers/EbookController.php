<?php

namespace App\Http\Controllers;

use App\Models\EbookContent;
use App\Models\Member;
use App\Models\PharmacyLogo;
use App\Models\PointVisit;
use App\Models\Reseller;
use App\Models\SiteVisit;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class EbookController extends Controller
{
    public function index(Request $request, $id_reseller = null)
    {
        $this->captureReferral($request, $id_reseller);

        // Data halaman depan sama untuk semua pengunjung, jadi disimpan di cache.
        // Cache otomatis dihapus saat admin mengubah konten/logo/testimoni
        // (lihat EbookContent::LANDING_CACHE_KEY dan event booted() di model).
        $landing = Cache::remember(EbookContent::LANDING_CACHE_KEY, now()->addHours(6), function () {
            $covers = [];
            $coverDirectory = public_path('coverebook');

            if (File::exists($coverDirectory)) {
                foreach (File::files($coverDirectory) as $file) {
                    if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        $covers[] = 'coverebook/' . $file->getFilename();
                    }
                }
            }

            if (empty($covers) && File::exists(public_path('coverebook.png'))) {
                $covers[] = 'coverebook.png';
            }

            $content = EbookContent::query()->first() ?? new EbookContent(EbookContent::defaultData());
            $content->chapters = EbookContent::normalizeChapters($content->chapters ?? []);

            $pharmacyLogos = Schema::hasTable('pharmacy_logos')
                ? PharmacyLogo::query()->orderBy('sort_order')->orderBy('id')->get()
                : collect();

            $testimonials = Schema::hasTable('testimonials')
                ? Testimonial::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get()
                : collect();

            return compact('covers', 'content', 'pharmacyLogos', 'testimonials');
        });

        $member = $this->resolveActiveMember($request);
        $totalVisits = $this->trackVisit($request);

        // Harga ditampilkan di halaman depan. Pengunjung dari link reseller/promo
        // langsung melihat harga promonya.
        $priceOriginal = (int) $landing['content']->payment_price_original;
        $referralId = (int) $request->session()->get('referral_id_reseller', 0);
        $priceToPay = $referralId > 0 ? EbookContent::priceFor($referralId) : $priceOriginal;

        return view('ebook.index', [
            'priceOriginal' => $priceOriginal,
            'priceToPay' => $priceToPay,
            'hasPromoCode' => (int) $landing['content']->payment_price_final < $priceOriginal,
            'waHelpLink' => Member::businessWhatsappLink(
                "Halo, saya tertarik dengan E-Book Bisnis Apotek Itu Asik.\nBoleh saya bertanya dulu sebelum membeli?"
            ),
            'covers' => $landing['covers'],
            'content' => $landing['content'],
            'isMember' => $member !== null,
            'memberName' => $member?->name,
            'pharmacyLogos' => $landing['pharmacyLogos'],
            'testimonials' => $landing['testimonials'],
            'totalVisits' => $totalVisits,
        ]);
    }

    public function point(Request $request, string $slug)
    {
        $content = Cache::remember(EbookContent::CONTENT_CACHE_KEY, now()->addHours(6), function () {
            return EbookContent::query()->first() ?? new EbookContent(EbookContent::defaultData());
        });

        $chapters = EbookContent::normalizeChapters($content->chapters ?? []);
        $flatPoints = [];
        $member = $this->resolveActiveMember($request);
        $isMember = $member !== null;

        $selectedChapter = null;
        $selectedPoint = null;
        $selectedChapterIndex = null;

        foreach ($chapters as $chapterIndex => $chapter) {
            foreach ($chapter['items'] as $point) {
                $flatPoints[] = [
                    'chapter' => $chapter,
                    'point' => $point,
                    'chapter_index' => $chapterIndex,
                ];

                if (($point['slug'] ?? '') === $slug) {
                    $selectedChapter = $chapter;
                    $selectedPoint = $point;
                    $selectedChapterIndex = $chapterIndex;
                }
            }
        }

        abort_if(!$selectedPoint, 404);

        $isPointLocked = (($selectedChapterIndex ?? 0) > 0) && !$isMember;

        $readablePoints = $flatPoints;

        $currentIndex = collect($readablePoints)->search(fn (array $entry) => ($entry['point']['slug'] ?? '') === $slug);
        $previousPoint = $currentIndex !== false && $currentIndex > 0 ? $readablePoints[$currentIndex - 1] : null;
        $nextPoint = $currentIndex !== false && $currentIndex < count($readablePoints) - 1 ? $readablePoints[$currentIndex + 1] : null;
        $pointNumber = $currentIndex !== false ? $currentIndex + 1 : null;
        $totalPoints = count($readablePoints);
        $youtubeEmbedUrl = $this->resolveYoutubeEmbedUrl((string) ($selectedPoint['youtube_url'] ?? ''));
        $this->trackVisit($request);
        $this->trackPointVisit($request, $slug, (string) ($selectedPoint['title'] ?? $slug));

        return view('ebook.point', [
            'content' => $content,
            'chapter' => $selectedChapter,
            'point' => $selectedPoint,
            'chapterPoints' => $selectedChapter['items'] ?? [],
            'previousPoint' => $previousPoint,
            'nextPoint' => $nextPoint,
            'pointNumber' => $pointNumber,
            'totalPoints' => $totalPoints,
            'youtubeEmbedUrl' => $youtubeEmbedUrl,
            'isMember' => $isMember,
            'memberName' => $member?->name,
            'isPointLocked' => $isPointLocked,
        ]);
    }

    private function resolveYoutubeEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        $parts = parse_url($url);
        if (!is_array($parts)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');
        $videoId = null;

        if (str_contains($host, 'youtu.be')) {
            $videoId = trim($path, '/');
        } elseif (str_contains($host, 'youtube.com')) {
            if (str_starts_with($path, '/watch')) {
                parse_str((string) ($parts['query'] ?? ''), $query);
                $videoId = (string) ($query['v'] ?? '');
            } elseif (str_starts_with($path, '/shorts/')) {
                $videoId = trim(substr($path, strlen('/shorts/')), '/');
            } elseif (str_starts_with($path, '/embed/')) {
                $videoId = trim(substr($path, strlen('/embed/')), '/');
            }
        }

        if (!is_string($videoId) || !preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
            return null;
        }

        return 'https://www.youtube.com/embed/' . $videoId;
    }

    private function trackVisit(Request $request): int
    {
        // Pengunjung yang sudah dihitung di sesi ini cukup membaca angka dari cache,
        // tanpa query ke database.
        if ($request->session()->get('site_visit_counted')) {
            return (int) Cache::remember('ebook.total_visits', now()->addMinutes(10), function () {
                return (int) SiteVisit::query()->value('total_visits');
            });
        }

        $siteVisit = SiteVisit::query()->firstOrCreate([], ['total_visits' => 0]);
        $siteVisit->increment('total_visits');
        $request->session()->put('site_visit_counted', true);
        Cache::put('ebook.total_visits', (int) $siteVisit->total_visits, now()->addMinutes(10));

        return (int) $siteVisit->total_visits;
    }

    private function trackPointVisit(Request $request, string $slug, string $title): void
    {
        $pointVisit = PointVisit::query()->firstOrCreate(
            ['slug' => $slug],
            ['title' => $title, 'visit_count' => 0]
        );

        if ($pointVisit->title !== $title) {
            $pointVisit->update(['title' => $title]);
        }

        $sessionKey = 'point_visit_counted_' . $slug;

        if (! $request->session()->get($sessionKey)) {
            $pointVisit->increment('visit_count');
            $request->session()->put($sessionKey, true);
        }
    }

    private function captureReferral(Request $request, $id_reseller = null): void
    {
        if ($id_reseller === null) {
            return;
        }

        $isValidReseller = Reseller::query()
            ->where('id_reseller', $id_reseller)
            ->where('is_active', true)
            ->exists();

        if ($isValidReseller) {
            $request->session()->put('referral_id_reseller', $id_reseller);
        }
    }

    private function resolveActiveMember(Request $request): ?Member
    {
        $memberId = (int) $request->session()->get('member_id', 0);
        if ($memberId <= 0) {
            return null;
        }

        return Member::query()
            ->whereKey($memberId)
            ->where('is_active', true)
            ->first();
    }
}
