<?php

namespace NmDigitalHub\SumitPayment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'card_type',
        'card_last4',
        'card_brand',
        'exp_month',
        'exp_year',
        'citizen_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'token' => 'encrypted',
        'citizen_id' => 'encrypted',
    ];

    protected $hidden = [
        'token',
        'citizen_id',
    ];

    /**
     * Get the table name
     */
    public function getTable(): string
    {
        return 'sumit_payment_tokens';
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        $expYear = (int) $this->exp_year;
        $expMonth = (int) $this->exp_month;

        if ($expYear < $currentYear) {
            return true;
        }

        if ($expYear == $currentYear && $expMonth < $currentMonth) {
            return true;
        }

        return false;
    }

    /**
     * Get formatted expiration date
     */
    public function getFormattedExpiration(): string
    {
        return str_pad($this->exp_month, 2, '0', STR_PAD_LEFT) . '/' . $this->exp_year;
    }

    /**
     * Get masked card number
     */
    public function getMaskedCardNumber(): string
    {
        return '****' . $this->card_last4;
    }

    /**
     * Scope for active (non-expired) tokens
     */
    public function scopeActive($query)
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        return $query->where(function ($q) use ($currentYear, $currentMonth) {
            $q->where('exp_year', '>', $currentYear)
              ->orWhere(function ($q2) use ($currentYear, $currentMonth) {
                  $q2->where('exp_year', '=', $currentYear)
                     ->where('exp_month', '>=', $currentMonth);
              });
        });
    }

    /**
     * Scope for default tokens
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
