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

    public const KEY_ABOUT_TITLE = 'about_title';

    public const KEY_ABOUT_SUBTITLE = 'about_subtitle';

    public const KEY_ABOUT_HEADING = 'about_heading';

    public const KEY_ABOUT_DESCRIPTION = 'about_description';

    public const KEY_ABOUT_IMAGE = 'about_image';

    public array $translatable = ['value'];

    protected $fillable = [
        'key',
        'value',
    ];
}
