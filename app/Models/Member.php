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
        'google_id',
        'avatar',
        'password',
        'payment_proof_path',
        'is_active',
        'paid_at',
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
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new MemberResetPasswordNotification($token));
    }
}
