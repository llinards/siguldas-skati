
<?php

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Services\FileStorageService;
use App\Services\GalleryServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    // Mock the FileStorageService
    $this->fileStorageService = $this->mock(FileStorageService::class);
    $this->galleryServices = new GalleryServices($this->fileStorageService);
});

test('getAllGalleries returns collection of all galleries regardless of status', function () {
    Gallery::factory()->count(3)->create(['is_active' => true]);
    Gallery::factory()->count(2)->create(['is_active' => false]);

    $galleries = $this->galleryServices->getAllGalleries();

    expect($galleries)->toHaveCount(5)
        ->and($galleries->where('is_active', true))->toHaveCount(3)
        ->and($galleries->where('is_active', false))->toHaveCount(2);
});

test('getAllActiveGalleries returns only active galleries', function () {
    Gallery::factory()->count(3)->create(['is_active' => true]);
    Gallery::factory()->count(2)->create(['is_active' => false]);

    $activeGalleries = $this->galleryServices->getAllActiveGalleries();

    expect($activeGalleries)->toHaveCount(3);

    $activeGalleries->each(function ($gallery) {
        expect($gallery->is_active)->toBeTrue();
    });
});

test('getGalleryById returns correct gallery when exists', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Test Gallery'],
        'is_active' => true,
    ]);

    $foundGallery = $this->galleryServices->getGalleryById($gallery->id);

    expect($foundGallery)->not()->toBeNull()
        ->and($foundGallery->id)->toBe($gallery->id)
        ->and($foundGallery->title)->toBe($gallery->title);
});

test('getGalleryById returns null when gallery does not exist', function () {
    $nonExistentGallery = $this->galleryServices->getGalleryById(999);

    expect($nonExistentGallery)->toBeNull();
});

test('createGallery creates new gallery with provided data', function () {
    $galleryData = [
        'title' => ['lv' => 'New Gallery'],
        'is_active' => true,
    ];

    $gallery = $this->galleryServices->createGallery($galleryData);

    expect($gallery)->toBeInstanceOf(Gallery::class)
        ->and($gallery->getTranslation('title', 'lv'))->toBe($galleryData['title']['lv'])
        ->and($gallery->is_active)->toBe($galleryData['is_active'])
        ->and($gallery->exists)->toBeTrue();

    $this->assertDatabaseHas('galleries', [
        'id' => $gallery->id,
        'title' => json_encode($galleryData['title']),
        'is_active' => 1,
    ]);
});

test('updateGallery updates gallery with new data', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Old Title'],
        'is_active' => false,
    ]);

    $updateData = [
        'title' => ['lv' => 'Updated Title'],
        'is_active' => true,
    ];

    $result = $this->galleryServices->updateGallery($gallery, $updateData);

    expect($result)->toBeTrue();

    $gallery->refresh();
    expect($gallery->getTranslation('title', 'lv'))->toBe($updateData['title']['lv'])
        ->and($gallery->is_active)->toBe($updateData['is_active']);
});

test('deleteGallery removes gallery and its image files', function () {
    $gallery = Gallery::factory()->create();

    // Create gallery images
    $image1 = GalleryImage::factory()->create([
        'gallery_id' => $gallery->id,
        'filename' => 'gallery-images/image1.jpg',
    ]);
    $image2 = GalleryImage::factory()->create([
        'gallery_id' => $gallery->id,
        'filename' => 'gallery-images/image2.jpg',
    ]);

    // Mock file deletion for each image
    $this->fileStorageService->shouldReceive('deleteFile')
        ->with('gallery-images/image1.jpg')
        ->once();

    $this->fileStorageService->shouldReceive('deleteFile')
        ->with('gallery-images/image2.jpg')
        ->once();

    $result = $this->galleryServices->deleteGallery($gallery);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    $this->assertDatabaseMissing('gallery_images', ['id' => $image1->id]);
    $this->assertDatabaseMissing('gallery_images', ['id' => $image2->id]);
});

test('toggleGalleryStatus toggles active to inactive', function () {
    $gallery = Gallery::factory()->create(['is_active' => true]);

    $result = $this->galleryServices->toggleGalleryStatus($gallery->id);

    expect($result)->toBeTrue();

    $gallery->refresh();
    expect($gallery->is_active)->toBeFalse();
});

test('toggleGalleryStatus toggles inactive to active', function () {
    $gallery = Gallery::factory()->create(['is_active' => false]);

    $result = $this->galleryServices->toggleGalleryStatus($gallery->id);

    expect($result)->toBeTrue();

    $gallery->refresh();
    expect($gallery->is_active)->toBeTrue();
});

test('toggleGalleryStatus returns false for non-existent gallery', function () {
    $result = $this->galleryServices->toggleGalleryStatus(999);

    expect($result)->toBeFalse();
});

test('storeGalleryImage stores image and creates database record', function () {
    $gallery = Gallery::factory()->create();
    $uploadedFile = UploadedFile::fake()->image('test-image.jpg');
    $storedPath = 'gallery-images/stored-image.jpg';

    $this->fileStorageService->shouldReceive('storeFile')
        ->with($uploadedFile, FileStorageService::GALLERY_IMAGE_PATH)
        ->once()
        ->andReturn($storedPath);

    $galleryImage = $this->galleryServices->storeGalleryImage($gallery->id, $uploadedFile);

    expect($galleryImage)->toBeInstanceOf(GalleryImage::class)
        ->and($galleryImage->gallery_id)->toBe($gallery->id)
        ->and($galleryImage->filename)->toBe($storedPath)
        ->and($galleryImage->exists)->toBeTrue();

    $this->assertDatabaseHas('gallery_images', [
        'gallery_id' => $gallery->id,
        'filename' => $storedPath,
    ]);
});

test('deleteGalleryImage removes image file and database record', function () {
    $gallery = Gallery::factory()->create();
    $galleryImage = GalleryImage::factory()->create([
        'gallery_id' => $gallery->id,
        'filename' => 'gallery-images/test-image.jpg',
    ]);

    $this->fileStorageService->shouldReceive('deleteFile')
        ->with('gallery-images/test-image.jpg')
        ->once();

    $result = $this->galleryServices->deleteGalleryImage($galleryImage->id);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('gallery_images', ['id' => $galleryImage->id]);
});

test('deleteGalleryImage throws exception for non-existent image', function () {
    $this->fileStorageService->shouldNotReceive('deleteFile');

    expect(fn () => $this->galleryServices->deleteGalleryImage(999))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

test('updateImageOrder updates order for multiple images', function () {
    $gallery = Gallery::factory()->create();
    $image1 = GalleryImage::factory()->create(['gallery_id' => $gallery->id, 'order' => 1]);
    $image2 = GalleryImage::factory()->create(['gallery_id' => $gallery->id, 'order' => 2]);
    $image3 = GalleryImage::factory()->create(['gallery_id' => $gallery->id, 'order' => 3]);

    $orderData = [
        ['value' => $image3->id, 'order' => 1],
        ['value' => $image1->id, 'order' => 2],
        ['value' => $image2->id, 'order' => 3],
    ];

    $this->galleryServices->updateImageOrder($orderData);

    $image1->refresh();
    $image2->refresh();
    $image3->refresh();

    expect($image3->order)->toBe(1)
        ->and($image1->order)->toBe(2)
        ->and($image2->order)->toBe(3);
});

test('updateImageOrder throws exception for non-existent image', function () {
    $orderData = [
        ['value' => 999, 'order' => 1],
    ];

    expect(fn () => $this->galleryServices->updateImageOrder($orderData))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

test('getAllGalleries returns galleries ordered by order column', function () {
    Gallery::factory()->create(['order' => 3]);
    Gallery::factory()->create(['order' => 1]);
    Gallery::factory()->create(['order' => 2]);

    $galleries = $this->galleryServices->getAllGalleries();

    expect($galleries->first()->order)->toBe(1)
        ->and($galleries->get(1)->order)->toBe(2)
        ->and($galleries->last()->order)->toBe(3);
});

test('getAllActiveGalleries returns only active galleries ordered by order column', function () {
    Gallery::factory()->create(['order' => 3, 'is_active' => true]);
    Gallery::factory()->create(['order' => 1, 'is_active' => false]);
    Gallery::factory()->create(['order' => 2, 'is_active' => true]);

    $activeGalleries = $this->galleryServices->getAllActiveGalleries();

    expect($activeGalleries)->toHaveCount(2)
        ->and($activeGalleries->first()->order)->toBe(2)
        ->and($activeGalleries->last()->order)->toBe(3);
});

test('storeGalleryImage creates image with default order', function () {
    $gallery = Gallery::factory()->create();
    $uploadedFile = UploadedFile::fake()->image('test-image.jpg');
    $storedPath = 'gallery-images/stored-image.jpg';

    $this->fileStorageService->shouldReceive('storeFile')
        ->once()
        ->andReturn($storedPath);

    $galleryImage = $this->galleryServices->storeGalleryImage($gallery->id, $uploadedFile);

    expect($galleryImage->order)->toBe(null);
});

test('deleteGallery with empty images collection', function () {
    $gallery = Gallery::factory()->create();

    // No images created for this gallery
    $this->fileStorageService->shouldNotReceive('deleteFile');

    $result = $this->galleryServices->deleteGallery($gallery);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
});

test('createGallery handles translatable title correctly', function () {
    $galleryData = [
        'title' => [
            'lv' => 'Latviešu nosaukums',
            'en' => 'English title',
        ],
        'is_active' => true,
    ];

    $gallery = $this->galleryServices->createGallery($galleryData);

    expect($gallery->getTranslation('title', 'lv'))->toBe('Latviešu nosaukums')
        ->and($gallery->getTranslation('title', 'en'))->toBe('English title');
});

test('returns empty collections when no galleries match criteria', function () {
    // Test with no galleries at all
    expect($this->galleryServices->getAllActiveGalleries())->toBeEmpty()
        ->and($this->galleryServices->getAllGalleries())->toBeEmpty();

    // Test with only inactive galleries
    Gallery::factory()->count(3)->create(['is_active' => false]);
    expect($this->galleryServices->getAllActiveGalleries())->toBeEmpty();
});
