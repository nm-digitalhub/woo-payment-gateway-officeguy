<?php

namespace NmDigitalHub\SumitPayment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringBilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_token_id',
        'amount',
        'currency',
        'vat_rate',
        'frequency',
        'description',
        'customer_name',
        'customer_email',
        'status',
        'next_payment_date',
        'last_payment_date',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'next_payment_date' => 'datetime',
        'last_payment_date' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the table name
     */
    public function getTable(): string
    {
        return 'sumit_recurring_billings';
    }

    /**
     * Get the payment token associated with this billing
     */
    public function paymentToken()
    {
        return $this->belongsTo(PaymentToken::class);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for cancelled subscriptions
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for due payments
     */
    public function scopeDue($query)
    {
        return $query->where('status', 'active')
                     ->where('next_payment_date', '<=', now());
    }

    /**
     * Check if billing is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if payment is due
     */
    public function isDue(): bool
    {
        return $this->isActive() && $this->next_payment_date <= now();
    }
}
