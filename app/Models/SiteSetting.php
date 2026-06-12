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

    public const KEY_PRODUCTS_TITLE = 'products_title';

    public const KEY_PRODUCTS_SUBTITLE = 'products_subtitle';

    public const KEY_GALLERY_TITLE = 'gallery_title';

    public const KEY_GALLERY_SUBTITLE = 'gallery_subtitle';

    public const KEY_EXPERIENCES_TITLE = 'experiences_title';

    public const KEY_EXPERIENCES_SUBTITLE = 'experiences_subtitle';

    public const KEY_ACTIVITIES_TITLE = 'activities_title';

    public const KEY_ACTIVITIES_SUBTITLE = 'activities_subtitle';

    public const KEY_FAQ_TITLE = 'faq_title';

    public const KEY_FAQ_SUBTITLE = 'faq_subtitle';

    public array $translatable = ['value'];

    protected $fillable = [
        'key',
        'value',
    ];
}
