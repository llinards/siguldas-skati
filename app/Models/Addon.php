<?php

namespace App\Models;

use App\Enums\AddonPricingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Addon extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = ['product_id', 'name', 'description', 'price', 'pricing_type', 'is_active', 'order'];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'price' => 'integer',
        'pricing_type' => AddonPricingType::class,
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', static function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
