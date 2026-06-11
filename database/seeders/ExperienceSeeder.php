<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Services\FileStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        if (Experience::query()->exists()) {
            return;
        }

        $experiences = [
            [
                'icon' => 'wave.svg',
                'title' => ['lv' => 'Klusums un miers', 'en' => 'Silence and peace'],
                'description' => [
                    'lv' => '<p>Mūsu brīvdienu dizaina mājās nav steigas – šī ir vieta, kur Tu vari elpot dziļāk, dzirdēt sevi un atpūsties bez stresa.</p>',
                    'en' => '<p>In our holiday design homes there is no rush – this is a place where you can breathe deeper, hear yourself, and relax without stress.</p>',
                ],
            ],
            [
                'icon' => 'check.svg',
                'title' => ['lv' => 'Estētika un komforts', 'en' => 'Aesthetics and comfort'],
                'description' => [
                    'lv' => '<p>Pārdomāts dizains, kvalitatīvas detaļas un mājīgums, kas ļauj justies kā mājās – tikai vēl labāk.</p>',
                    'en' => '<p>Thoughtful design, quality details, and coziness that make you feel at home – only better.</p>',
                ],
            ],
            [
                'icon' => 'happy_face.svg',
                'title' => ['lv' => 'Atmiņas un sajūtas', 'en' => 'Memories and experiences'],
                'description' => [
                    'lv' => '<p>Šī nav tikai naktsmītne – tā ir iespēja apstāties, sajust vidi un ieraudzīt Siguldu citām acīm.</p>',
                    'en' => '<p>This is not just accommodation – it’s an opportunity to pause, feel the environment, and see Sigulda with new eyes.</p>',
                ],
            ],
            [
                'icon' => 'location.svg',
                'title' => ['lv' => 'Izcila lokācija', 'en' => 'Excellent location'],
                'description' => [
                    'lv' => '<p>Vietu pašā Siguldas sirdī, kur daba un pilsētas kultūras notikumi satiekas viena soļa attālumā.</p>',
                    'en' => '<p>In the very heart of Sigulda, where nature and the city’s cultural events meet just steps away.</p>',
                ],
            ],
        ];

        foreach ($experiences as $index => $data) {
            $experience = new Experience([
                'icon_image' => $this->seedIcon($data['icon']),
                'order' => $index,
                'is_active' => true,
            ]);
            $experience->setTranslations('title', $data['title']);
            $experience->setTranslations('description', $data['description']);
            $experience->save();
        }
    }

    private function seedIcon(string $filename): ?string
    {
        $storagePath = FileStorageService::EXPERIENCE_ICON_PATH.'/'.$filename;
        $sourcePath = public_path('icons/'.$filename);

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }
}
