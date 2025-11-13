<?php

namespace NmDigitalhub\WooPaymentGatewayAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'last_four',
        'card_type',
        'expiry_date',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'expiry_date' => 'date',
        ];
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
