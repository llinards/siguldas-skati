<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'icon_image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', static function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
