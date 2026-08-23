<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'total_visits',
    ];

    protected function casts(): array
    {
        return [
            'total_visits' => 'integer',
        ];
    }
}
