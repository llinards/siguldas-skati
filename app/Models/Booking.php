<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'nights_total' => 'integer',
        'grand_total' => 'integer',
        'status' => BookingStatus::class,
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refund_amount' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'booking_addon')
            ->withPivot(['name', 'price', 'pricing_type', 'quantity'])
            ->withTimestamps();
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'SS-'.Str::upper(Str::random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }
}
