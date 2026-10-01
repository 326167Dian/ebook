<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MidtransPayment extends Model
{
    protected $fillable = [
        'flow',
        'order_id',
        'midtrans_transaction_id',
        'member_id',
        'pending_name',
        'pending_email',
        'id_reseller',
        'amount',
        'qr_string',
        'qr_image_url',
        'status',
        'paid_at',
        'expires_at',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_checked_at' => 'datetime',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'id_reseller', 'id_reseller');
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isPast();
    }

    /**
     * Compares what Midtrans confirms as paid (gross_amount, a decimal string like
     * "100000.00") against what we requested when the charge was created.
     */
    public function receivedAmountMatches($grossAmount): bool
    {
        if ($grossAmount === null || $grossAmount === '') {
            return false;
        }

        return (int) round((float) $grossAmount) === (int) $this->amount;
    }

    /**
     * Mark this payment as paid and activate the related member.
     * Idempotent so both browser-polling and the Midtrans webhook can call it safely.
     */
    public function markAsPaid(): void
    {
        if ($this->status === 'paid') {
            return;
        }

        $this->status = 'paid';
        $this->paid_at = $this->paid_at ?: now();
        $this->save();

        if ($this->flow === 'register') {
            if (Member::query()->where('email', $this->pending_email)->exists()) {
                return;
            }

            try {
                Member::query()->create([
                    'name' => $this->pending_name,
                    'email' => $this->pending_email,
                    'password' => Hash::make(Str::random(40)),
                    'payment_proof_path' => 'midtrans:' . $this->order_id,
                    'is_active' => true,
                    'paid_at' => $this->paid_at,
                    'id_reseller' => $this->id_reseller,
                ]);
            } catch (QueryException $exception) {
                // Unique email race between webhook and browser polling both finalizing
                // at the same time - the other path already created the member, ignore.
            }

            return;
        }

        $member = Member::query()->find($this->member_id);

        if ($member) {
            $member->forceFill([
                'payment_proof_path' => 'midtrans:' . $this->order_id,
                'is_active' => true,
                'paid_at' => $this->paid_at,
                'payment_rejected_at' => null,
                'id_reseller' => $this->id_reseller,
            ])->save();
        }
    }
}
