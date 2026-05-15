<?php

namespace Database\Seeders;

use App\Models\HeaderMedia;
use App\Services\FileStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HeaderMediaSeeder extends Seeder
{
    public function run(): void
    {
        if (HeaderMedia::query()->exists()) {
            return;
        }

        $sourceDir = public_path('images');
        $disk = Storage::disk('public');

        for ($i = 1; $i <= 7; $i++) {
            $source = $sourceDir.DIRECTORY_SEPARATOR."header-image{$i}.jpg";

            if (! is_file($source)) {
                continue;
            }

            $targetPath = FileStorageService::HEADER_IMAGE_PATH.'/header-image-'.$i.'-'.uniqid().'.jpg';
            $disk->put($targetPath, file_get_contents($source));

            HeaderMedia::create([
                'filename' => $targetPath,
                'order' => $i - 1,
                'type' => HeaderMedia::TYPE_IMAGE,
            ]);
        }
    }
}
