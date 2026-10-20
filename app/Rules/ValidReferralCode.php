<?php

namespace App\Rules;

use App\Models\Reseller;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Kode referral boleh berupa ID reseller (angka, misal "1") atau kode promo
 * huruf+angka (misal "APOTEK11") yang tersimpan di kolom reseller.promo_code.
 * Reseller harus aktif.
 */
class ValidReferralCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Reseller::idFromReferralCode($value) === null) {
            $fail('Kode referral tidak ditemukan, silakan masukkan kode yang benar.');
        }
    }
}
