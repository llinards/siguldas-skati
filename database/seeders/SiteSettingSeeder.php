<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Services\FileStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            SiteSetting::KEY_HOME_HERO_TITLE => [
                'lv' => 'Modernas brīvdienu dizaina mājas tavai atpūtai!',
                'en' => 'Modern holiday design houses for your relaxation!',
            ],
            SiteSetting::KEY_ABOUT_TITLE => [
                'lv' => 'Par mums',
                'en' => 'About us',
            ],
            SiteSetting::KEY_ABOUT_SUBTITLE => [
                'lv' => 'Klusuma greznība Siguldas sirdī!',
                'en' => 'The luxury of silence in the heart of Sigulda!',
            ],
            SiteSetting::KEY_ABOUT_HEADING => [
                'lv' => 'Siguldas skati',
                'en' => 'Siguldas Skati',
            ],
            SiteSetting::KEY_ABOUT_DESCRIPTION => [
                'lv' => '<p>Īpaša atpūtas vieta tiem, kuri meklē mieru, klātbūtnes un skaistuma sajūtu pašā Siguldas sirdī. Mūsu stāsts sākas vietā, kur dizains saplūst ar dabu un miers kļūst par lielāko greznību.</p>'
                    .'<p>Atrodoties tieši līdzās Panorāmas ratam un Svētku laukumam, esam radījuši modernas brīvdienu dizaina mājas, kas piedāvā ne tikai naktsmītni, bet arī sajūtu pieredzi. Šeit ainava kļūst par interjera daļu, un katrs gadalaiks sniedz jaunu skatpunktu – no miglainiem rudens rītiem līdz sniegotām virsotnēm vai saulainām vasaras dienām.</p>'
                    .'<p>Mēs piedāvājam vietu, kur vienkāršība nozīmē kvalitāti, minimālisms – apzinātu komfortu, un katrā detaļā jūtama mīlestība pret vietu, kur dzīvojam. Šis projekts ir mūsu aicinājums atgriezties pie tā, kas būtisks – miera, klātbūtnes un skaistuma.</p>',
                'en' => '<p>A special retreat for those seeking peace, presence, and a sense of beauty in the very heart of Sigulda. Our story begins in a place where design blends with nature and tranquility becomes the greatest luxury.</p>'
                    .'<p>Located right next to the Ferris Wheel and Celebration Square, we have created modern holiday design houses that offer not only accommodation but also an experience of feeling. Here, the landscape becomes part of the interior, and each season brings a new perspective – from misty autumn mornings to snowy peaks or sunny summer days.</p>'
                    .'<p>We offer a place where simplicity means quality, minimalism reflects mindful comfort, and every detail carries a love for the place we call home. This project is our invitation to return to what truly matters – peace, presence, and beauty.</p>',
            ],
            SiteSetting::KEY_PRODUCTS_TITLE => [
                'lv' => 'Dizaina mājas, sauna un džakuzi',
                'en' => 'Design houses, sauna and jacuzzi',
            ],
            SiteSetting::KEY_PRODUCTS_SUBTITLE => [
                'lv' => 'Izsmalcināta atpūta starp pilsētu un dabu!',
                'en' => 'Sophisticated relaxation between city and nature!',
            ],
            SiteSetting::KEY_GALLERY_TITLE => [
                'lv' => 'Galerija',
                'en' => 'Gallery',
            ],
            SiteSetting::KEY_GALLERY_SUBTITLE => [
                'lv' => 'Siguldas skatu galerija.',
                'en' => 'Siguldas Skati gallery.',
            ],
            SiteSetting::KEY_EXPERIENCES_TITLE => [
                'lv' => 'Ko sniedz pieredze Siguldas Skatos?',
                'en' => 'What does the experience at Siguldas Skati offer?',
            ],
            SiteSetting::KEY_EXPERIENCES_SUBTITLE => [
                'lv' => 'Dizaina brīvdienu mājas ar skatu uz Siguldu!',
                'en' => 'Design holiday houses with a view of Sigulda!',
            ],
            SiteSetting::KEY_ACTIVITIES_TITLE => [
                'lv' => 'Ko vēl darīt Siguldā?',
                'en' => 'What else to do in Sigulda?',
            ],
            SiteSetting::KEY_ACTIVITIES_SUBTITLE => [
                'lv' => 'Sigulda ir vieta, kur daba, kultūra un piedzīvojumi saplūst vienā ainavā. Neatkarīgi no gadalaika, šeit katrs var atrast sev piemērotu ritmu – vai tā būtu nesteidzīga pastaiga dabas takās, kultūras baudījums vai mazs piedzīvojums virs koku galotnēm.',
                'en' => 'Sigulda is a place where nature, culture, and adventures blend into one landscape. No matter the season, everyone can find their own rhythm here – whether it’s a leisurely walk along nature trails, enjoying cultural events, or a small adventure above the treetops.',
            ],
        ];

        foreach ($defaults as $key => $translations) {
            if (SiteSetting::where('key', $key)->exists()) {
                continue;
            }

            $setting = new SiteSetting(['key' => $key]);
            $setting->setTranslations('value', $translations);
            $setting->save();
        }

        $this->seedAboutImage();
    }

    private function seedAboutImage(): void
    {
        if (SiteSetting::where('key', SiteSetting::KEY_ABOUT_IMAGE)->exists()) {
            return;
        }

        $storagePath = FileStorageService::ABOUT_IMAGE_PATH.'/siguldas-skati-home-5.jpg';
        $sourcePath = public_path('images/siguldas-skati-home-5.jpg');

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        $setting = new SiteSetting(['key' => SiteSetting::KEY_ABOUT_IMAGE]);
        $setting->setTranslations('value', ['lv' => $storagePath, 'en' => $storagePath]);
        $setting->save();
    }
}
