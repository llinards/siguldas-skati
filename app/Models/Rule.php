<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Rule extends Model
{
    use HasFactory;
    use HasTranslations;

    public const SECTION_HOUSE = 'house';

    public const SECTION_SAFETY = 'safety';

    public const SECTION_PROHIBITED = 'prohibited';

    public array $translatable = ['title'];

    protected $fillable = [
        'title',
        'icon_image',
        'is_active',
        'order',
        'section',
    ];

    protected $casts = [
        'title' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', static function (Builder $builder) {
            $builder->orderBy('order');
        });
    }
}
