<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointVisit extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'visit_count',
    ];

    protected function casts(): array
    {
        return [
            'visit_count' => 'integer',
        ];
    }
}
