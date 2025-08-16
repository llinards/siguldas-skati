<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'slug', 'description', 'pricelist'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(Rule::class);
    }

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'person_count' => 'integer',
        'pricelist' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $product = $this->where(function ($query) use ($value) {
            $locales = array_keys(LaravelLocalization::getSupportedLocales());
            foreach ($locales as $locale) {
                $query->orWhere("slug->{$locale}", $value);
            }
        })
            ->where('is_active', 1)
            ->first();

        return $product;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', static function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
