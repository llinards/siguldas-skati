<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteSetting extends Model
{
    use HasFactory;
    use HasTranslations;

    public const KEY_HOME_HERO_TITLE = 'home_hero_title';

    public array $translatable = ['value'];

    protected $fillable = [
        'key',
        'value',
    ];
}
