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
        'promo_code',
        'custom_price_final',
        'password',
        'nm_reseller',
        'telp',
        'alamat',
        'bank',
        'rekening',
        'is_active',
        'commission_proof_path',
    ];

    protected $hidden = [
        'password',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'id_reseller', 'id_reseller');
    }

    /**
     * Ubah kode referral yang diketik konsumen menjadi id_reseller.
     * Menerima ID angka ("1") atau kode promo ("APOTEK11", tidak peka huruf besar/kecil).
     * Hanya reseller aktif. Mengembalikan null bila tidak ditemukan.
     */
    public static function idFromReferralCode(mixed $code): ?int
    {
        $code = strtoupper(trim((string) $code));

        if ($code === '' || strlen($code) > 20) {
            return null;
        }

        $query = static::query()->where('is_active', true);

        if (ctype_digit($code)) {
            $id = $query->clone()->where('id_reseller', (int) $code)->value('id_reseller');

            if ($id !== null) {
                return (int) $id;
            }
        }

        $id = $query->where('promo_code', $code)->value('id_reseller');

        return $id !== null ? (int) $id : null;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'custom_price_final' => 'integer',
        ];
    }
}
