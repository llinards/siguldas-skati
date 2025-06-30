<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'slug', 'description'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'title'       => 'array',
        'slug'        => 'array',
        'description' => 'array',
        'is_active'   => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $locale = app()->getLocale();

        return $this->where("slug->{$locale}", $value)
                    ->where('is_active', 1)
                    ->first();
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', static function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
