<?php

namespace App\Http\Controllers;

use App\Models\EbookContent;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EbookController extends Controller
{
    public function index(Request $request)
    {
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

        $content = EbookContent::query()->first();

        if (!$content) {
            $content = new EbookContent(EbookContent::defaultData());
        }

        $content->chapters = EbookContent::normalizeChapters($content->chapters ?? []);
        $member = $this->resolveActiveMember($request);

        return view('ebook.index', [
            'covers' => $covers,
            'content' => $content,
            'isMember' => $member !== null,
        ]);
    }

    public function point(Request $request, string $slug)
    {
        $content = EbookContent::query()->first();

        if (!$content) {
            $content = new EbookContent(EbookContent::defaultData());
        }

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

        return view('ebook.point', [
            'content' => $content,
            'chapter' => $selectedChapter,
            'point' => $selectedPoint,
            'chapterPoints' => $selectedChapter['items'] ?? [],
            'previousPoint' => $previousPoint,
            'nextPoint' => $nextPoint,
            'pointNumber' => $pointNumber,
            'totalPoints' => $totalPoints,
            'isMember' => $isMember,
            'isPointLocked' => $isPointLocked,
        ]);
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
