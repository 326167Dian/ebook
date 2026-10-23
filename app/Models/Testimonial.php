<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => EbookContent::flushPublicCache());
        static::deleted(fn () => EbookContent::flushPublicCache());
    }

    protected $fillable = [
        'name',
        'role',
        'content',
        'rating',
        'photo_path',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getInitialsAttribute(): string
    {
        $initials = collect(preg_split('/\s+/', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY))
            ->take(2)
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->implode('');

        return mb_strtoupper($initials !== '' ? $initials : '?');
    }
}
