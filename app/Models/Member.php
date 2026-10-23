<?php

namespace App\Models;

use App\Notifications\MemberResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Member extends Model implements CanResetPasswordContract
{
    use HasFactory;
    use Notifiable;
    use CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'google_id',
        'avatar',
        'password',
        'payment_proof_path',
        'is_active',
        'paid_at',
        'payment_rejected_at',
        'id_reseller',
        'commission_amount',
        'commission_proof_path',
        'commission_paid_at',
        'meta_fbp',
        'meta_fbc',
        'meta_client_ip',
        'meta_user_agent',
        'meta_source_url',
        'meta_purchase_sent_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'paid_at' => 'datetime',
            'payment_rejected_at' => 'datetime',
            'commission_amount' => 'integer',
            'commission_paid_at' => 'datetime',
            'meta_purchase_sent_at' => 'datetime',
        ];
    }

    /**
     * Ubah nomor HP yang diketik (0812..., +62 812..., 812...) jadi format 62812...
     */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }

    /**
     * Link wa.me ke nomor tertentu, dengan pesan yang sudah terisi.
     */
    public static function whatsappLink(?string $number, string $message = ''): ?string
    {
        $number = static::normalizePhone($number);

        if (! $number) {
            return null;
        }

        return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
    }

    /**
     * Link WhatsApp bisnis (tombol bantuan untuk pembeli).
     */
    public static function businessWhatsappLink(string $message = ''): ?string
    {
        return static::whatsappLink(config('services.whatsapp.number'), $message);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new MemberResetPasswordNotification($token));
    }
}
