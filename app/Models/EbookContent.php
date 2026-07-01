<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EbookContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge',
        'hero_title',
        'hero_description',
        'intro_note',
        'cover_image',
        'theme_primary',
        'theme_secondary',
        'theme_accent',
        'theme_bg_start',
        'theme_bg_end',
        'theme_text',
        'chapters',
    ];

    protected function casts(): array
    {
        return [
            'chapters' => 'array',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'badge' => 'Panduan Strategis',
            'hero_title' => 'Membangun Apotek yang Tumbuh dan Tahan Lama',
            'hero_description' => 'Dirancang untuk pemilik apotek baru maupun yang ingin naik kelas lewat sistem operasional, keuangan, pemasaran, dan pengembangan bisnis.',
            'intro_note' => 'E-book ini disusun untuk membantu Anda membangun bisnis apotek dengan kerangka yang praktis, terukur, dan bertahap. Fokus utamanya adalah menciptakan sistem kerja yang dapat dijalankan harian sekaligus siap dikembangkan ke level cabang, franchise, atau licensing.',
            'cover_image' => 'coverebook/coverebook.png',
            'theme_primary' => '#1e5fae',
            'theme_secondary' => '#4ea3e6',
            'theme_accent' => '#9fd8ff',
            'theme_bg_start' => '#f7fcff',
            'theme_bg_end' => '#dcebfa',
            'theme_text' => '#16314f',
            'chapters' => [
                [
                    'title' => 'Mindset Bisnis Apotek',
                    'items' => [
                        [
                            'title' => 'Mengapa bisnis apotek menarik',
                            'slug' => 'mengapa-bisnis-apotek-menarik',
                            'content' => 'Pembahasan mengenai peluang pasar, kebutuhan berulang, dan stabilitas bisnis apotek.',
                        ],
                        [
                            'title' => 'Kesalahan yang sering dilakukan pemilik baru',
                            'slug' => 'kesalahan-yang-sering-dilakukan-pemilik-baru',
                            'content' => 'Ringkasan kesalahan umum saat memulai apotek serta langkah antisipasinya.',
                        ],
                    ],
                ],
                [
                    'title' => 'Membuka Apotek',
                    'items' => [
                        ['title' => 'Legalitas', 'slug' => 'legalitas', 'content' => 'Panduan dokumen legal dan perizinan membuka apotek.'],
                        ['title' => 'Modal', 'slug' => 'modal', 'content' => 'Cara menghitung kebutuhan modal awal secara realistis.'],
                        ['title' => 'Pemilihan lokasi', 'slug' => 'pemilihan-lokasi', 'content' => 'Kriteria lokasi yang berdampak pada traffic dan omzet.'],
                        ['title' => 'Perencanaan stok', 'slug' => 'perencanaan-stok', 'content' => 'Strategi pembelian stok awal yang efisien dan aman.'],
                    ],
                ],
                [
                    'title' => 'Operasional Harian',
                    'items' => [
                        ['title' => 'SOP lengkap', 'slug' => 'sop-lengkap', 'content' => 'Standar operasional untuk kegiatan harian apotek.'],
                        ['title' => 'Pelayanan pasien', 'slug' => 'pelayanan-pasien', 'content' => 'Teknik pelayanan agar pasien puas dan kembali lagi.'],
                        ['title' => 'Pengelolaan resep', 'slug' => 'pengelolaan-resep', 'content' => 'Alur aman dan rapi dalam menerima serta menyiapkan resep.'],
                        ['title' => 'Manajemen stok', 'slug' => 'manajemen-stok', 'content' => 'Sistem kontrol stok agar tidak sering kosong atau overstock.'],
                    ],
                ],
                [
                    'title' => 'Keuangan',
                    'items' => [
                        ['title' => 'Menghitung laba bersih', 'slug' => 'menghitung-laba-bersih', 'content' => 'Rumus dan indikator untuk memantau laba bersih apotek.'],
                        ['title' => 'Mengelola arus kas', 'slug' => 'mengelola-arus-kas', 'content' => 'Cara menjaga cashflow tetap sehat setiap bulan.'],
                        ['title' => 'Menentukan target omzet', 'slug' => 'menentukan-target-omzet', 'content' => 'Metode menetapkan target omzet yang terukur.'],
                    ],
                ],
                [
                    'title' => 'Pemasaran',
                    'items' => [
                        ['title' => 'Cara mendapatkan pelanggan', 'slug' => 'cara-mendapatkan-pelanggan', 'content' => 'Langkah pemasaran awal untuk menarik pelanggan baru.'],
                        ['title' => 'Membangun loyalitas', 'slug' => 'membangun-loyalitas', 'content' => 'Strategi retensi pelanggan untuk pertumbuhan jangka panjang.'],
                        ['title' => 'Promosi yang sesuai etika profesi', 'slug' => 'promosi-yang-sesuai-etika-profesi', 'content' => 'Prinsip promosi apotek yang etis dan profesional.'],
                    ],
                ],
                [
                    'title' => 'Teknologi',
                    'items' => [
                        ['title' => 'Penggunaan software', 'slug' => 'penggunaan-software', 'content' => 'Manfaat software apotek untuk operasional cepat dan akurat.'],
                        ['title' => 'Dashboard', 'slug' => 'dashboard', 'content' => 'Metode membaca dashboard untuk keputusan harian.'],
                        ['title' => 'Laporan', 'slug' => 'laporan', 'content' => 'Jenis laporan penting yang wajib dipantau pemilik apotek.'],
                        ['title' => 'Analisis penjualan', 'slug' => 'analisis-penjualan', 'content' => 'Pendekatan analisis data penjualan untuk optimasi produk.'],
                    ],
                ],
                [
                    'title' => 'Pengembangan Bisnis',
                    'items' => [
                        ['title' => 'Membuka cabang', 'slug' => 'membuka-cabang', 'content' => 'Tahapan ekspansi ke cabang baru dengan risiko terkendali.'],
                        ['title' => 'Membangun tim', 'slug' => 'membangun-tim', 'content' => 'Struktur tim ideal untuk apotek yang sedang bertumbuh.'],
                        ['title' => 'Standardisasi operasional', 'slug' => 'standardisasi-operasional', 'content' => 'Standar proses agar kualitas layanan tetap konsisten.'],
                    ],
                ],
                [
                    'title' => 'Franchise / Licensing',
                    'items' => [
                        ['title' => 'Cara menjual sistem bisnis', 'slug' => 'cara-menjual-sistem-bisnis', 'content' => 'Kerangka mengemas sistem apotek menjadi produk bisnis.'],
                        ['title' => 'Model royalti', 'slug' => 'model-royalti', 'content' => 'Contoh model royalti yang adil dan berkelanjutan.'],
                        ['title' => 'Membership', 'slug' => 'membership', 'content' => 'Skema membership untuk mitra atau pelanggan loyal.'],
                        ['title' => 'Pendampingan', 'slug' => 'pendampingan', 'content' => 'Paket pendampingan agar implementasi sistem berhasil.'],
                    ],
                ],
                [
                    'title' => 'Lampiran',
                    'items' => [
                        ['title' => 'SOP', 'slug' => 'lampiran-sop', 'content' => 'Contoh SOP siap pakai untuk operasional apotek.'],
                        ['title' => 'Checklist', 'slug' => 'lampiran-checklist', 'content' => 'Checklist aktivitas harian dan mingguan apotek.'],
                        ['title' => 'Template', 'slug' => 'lampiran-template', 'content' => 'Template dokumen pendukung operasional bisnis apotek.'],
                        ['title' => 'Form', 'slug' => 'lampiran-form', 'content' => 'Kumpulan form administrasi yang mudah diadaptasi.'],
                        ['title' => 'KPI', 'slug' => 'lampiran-kpi', 'content' => 'Indikator kinerja utama untuk evaluasi bisnis apotek.'],
                        ['title' => 'Contoh laporan', 'slug' => 'lampiran-contoh-laporan', 'content' => 'Contoh laporan yang dibutuhkan untuk kontrol manajemen.'],
                    ],
                ],
            ],
        ];
    }

    public static function normalizeChapters(array $chapters): array
    {
        $usedSlugs = [];

        return collect($chapters)
            ->map(function ($chapter) use (&$usedSlugs) {
                $title = trim((string) data_get($chapter, 'title', ''));
                $items = collect(data_get($chapter, 'items', []))
                    ->map(function ($item) use (&$usedSlugs) {
                        $itemTitle = is_array($item) ? trim((string) data_get($item, 'title', '')) : trim((string) $item);

                        if ($itemTitle === '') {
                            return null;
                        }

                        $baseSlug = is_array($item) ? trim((string) data_get($item, 'slug', '')) : '';
                        $baseSlug = $baseSlug !== '' ? Str::slug($baseSlug) : Str::slug($itemTitle);
                        $baseSlug = $baseSlug !== '' ? $baseSlug : 'poin';

                        $slug = $baseSlug;
                        $counter = 2;

                        while (in_array($slug, $usedSlugs, true)) {
                            $slug = $baseSlug . '-' . $counter;
                            $counter++;
                        }

                        $usedSlugs[] = $slug;

                        $content = is_array($item) ? trim((string) data_get($item, 'content', '')) : '';

                        return [
                            'title' => $itemTitle,
                            'slug' => $slug,
                            'content' => $content,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'title' => $title,
                    'items' => $items,
                ];
            })
            ->filter(fn (array $chapter) => $chapter['title'] !== '' && !empty($chapter['items']))
            ->values()
            ->all();
    }
}
