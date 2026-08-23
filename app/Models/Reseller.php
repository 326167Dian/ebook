<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    use HasFactory;

    protected $table = 'reseller';

    protected $primaryKey = 'id_reseller';

    protected $fillable = [
        'username',
        'password',
        'nm_reseller',
        'telp',
        'alamat',
        'bank',
        'rekening',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
