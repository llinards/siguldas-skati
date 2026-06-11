<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Activity extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'modal_heading', 'modal_content'];

    protected $fillable = [
        'title',
        'modal_heading',
        'modal_content',
        'image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'title' => 'array',
        'modal_heading' => 'array',
        'modal_content' => 'array',
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
