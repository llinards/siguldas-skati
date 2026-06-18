<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    /**
     * Days before check-in a guest may still self-cancel for a full refund.
     */
    public const SELF_REFUND_DAYS = 7;

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

    public function formattedTotal(): string
    {
        return $this->formatCents($this->grand_total);
    }

    public function formattedRefund(): string
    {
        return $this->refund_amount === null ? '—' : $this->formatCents($this->refund_amount);
    }

    public function isRefundableByGuest(): bool
    {
        return $this->status === BookingStatus::Confirmed
            && now()->startOfDay()->lte($this->freeCancellationUntil());
    }

    /**
     * The last day a guest may self-cancel for a full refund.
     */
    public function freeCancellationUntil(): CarbonInterface
    {
        return $this->check_in->copy()->subDays(self::SELF_REFUND_DAYS)->startOfDay();
    }

    private function formatCents(int $cents): string
    {
        return number_format($cents / 100, 2, ',', ' ').' €';
    }
}
