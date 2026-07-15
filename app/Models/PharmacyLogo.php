<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyLogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
        'website_url',
        'sort_order',
    ];
}
