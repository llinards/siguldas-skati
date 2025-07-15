<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('created_at', static function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }
}
