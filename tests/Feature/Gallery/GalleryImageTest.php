
<?php

use App\Livewire\Admin\Gallery\GalleryImages;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');
});

test('component renders successfully when mounted with existing gallery', function () {
    $gallery = Gallery::factory()->create();

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->assertStatus(200)
        ->assertSet('gallery.id', $gallery->id)
        ->assertSet('images', []);
});

test('correctly identifies oversized images and calculates file sizes', function () {
    $gallery = Gallery::factory()->create();
    $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600); // 600KB > 512KB
    $normalImage = UploadedFile::fake()->image('normal.jpg')->size(100);   // 100KB < 512KB

    $component = Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$oversizedImage, $normalImage]);

    expect($component->instance()->isImageOversized(0))->toBeTrue()
        ->and($component->instance()->isImageOversized(1))->toBeFalse()
        ->and($component->instance()->getImageSizeInKB(0))->toBe(600)
        ->and($component->instance()->getImageSizeInKB(1))->toBe(100);
});

test('shows error message when uploading oversized images', function () {
    $gallery = Gallery::factory()->create();
    $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600);

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$oversizedImage])
        ->assertSessionHas('_flash.new.0', 'error');
});

test('clears error message when all images are within size limit', function () {
    $gallery = Gallery::factory()->create();
    $normalImage = UploadedFile::fake()->image('normal.jpg')->size(100);

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$normalImage])
        ->assertSessionMissing('error');
});

test('validates required images field and shows appropriate error messages', function () {
    $gallery = Gallery::factory()->create();

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [])
        ->call('store')
        ->assertHasErrors(['images' => 'required']);
});

test('successfully stores multiple images to storage and database with success message', function () {
    $gallery = Gallery::factory()->create();
    $image1 = UploadedFile::fake()->image('image1.jpg')->size(100);
    $image2 = UploadedFile::fake()->image('image2.jpg')->size(150);

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$image1, $image2])
        ->call('store')
        ->assertHasNoErrors()
        ->assertSessionHas('_flash.new.0', 'message')
        ->assertSet('images', []);

    // Verify images were stored in database
    expect(GalleryImage::where('gallery_id', $gallery->id)->count())->toBe(2);

    // Verify files were stored in storage
    $storedImages = GalleryImage::where('gallery_id', $gallery->id)->get();
    foreach ($storedImages as $storedImage) {
        Storage::disk('public')->assertExists($storedImage->filename);
    }
});

test('removes existing image from storage and database with success message', function () {
    $gallery = Gallery::factory()->create();

    // Create and store a fake image
    $imagePath = 'gallery-images/test-image.jpg';
    Storage::disk('public')->put($imagePath, 'fake-image-content');

    $galleryImage = GalleryImage::factory()->create([
        'gallery_id' => $gallery->id,
        'filename' => $imagePath,
    ]);

    // Verify image exists before deletion
    Storage::disk('public')->assertExists($imagePath);
    expect(GalleryImage::find($galleryImage->id))->not()->toBeNull();

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->call('removeImage', $galleryImage->id)
        ->assertSessionHas('_flash.new.0', 'message');

    // Verify image was removed from both storage and database
    Storage::disk('public')->assertMissing($imagePath);
    expect(GalleryImage::find($galleryImage->id))->toBeNull();
});

test('handles removal of image when file does not exist in storage', function () {
    $gallery = Gallery::factory()->create();

    $galleryImage = GalleryImage::factory()->create([
        'gallery_id' => $gallery->id,
        'filename' => 'non-existent-file.jpg',
    ]);

    // Verify the file doesn't exist in storage
    Storage::disk('public')->assertMissing('non-existent-file.jpg');

    // This should not throw an error
    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->call('removeImage', $galleryImage->id)
        ->assertSessionHas('_flash.new.0', 'message');

    // Verify image was removed from database
    expect(GalleryImage::find($galleryImage->id))->toBeNull();
});

test('removes new image from upload array and reorders indices correctly', function () {
    $gallery = Gallery::factory()->create();
    $image1 = UploadedFile::fake()->image('image1.jpg')->size(100);
    $image2 = UploadedFile::fake()->image('image2.jpg')->size(150);
    $image3 = UploadedFile::fake()->image('image3.jpg')->size(200);

    $component = Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$image1, $image2, $image3]);

    // Verify initial state
    expect($component->get('images'))->toHaveCount(3);

    // Remove middle image (index 1)
    $component->call('removeNewImage', 1);

    // Verify array was reordered properly
    expect($component->get('images'))->toHaveCount(2);

    // Verify the remaining images are correctly positioned
    $remainingImages = $component->get('images');
    expect($remainingImages[0]->getClientOriginalName())->toBe('image1.jpg')
        ->and($remainingImages[1]->getClientOriginalName())->toBe('image3.jpg');
});

test('re-checks for oversized images after removing new image', function () {
    $gallery = Gallery::factory()->create();
    $normalImage = UploadedFile::fake()->image('normal.jpg')->size(100);
    $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600);

    $component = Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$normalImage, $oversizedImage]);

    // Should have error due to oversized image
    $component->assertSessionHas('_flash.new.0', 'error');

    // Remove the oversized image
    $component->call('removeNewImage', 1);

    // Error should be cleared since no oversized images remain
    $component->assertSessionMissing('error');
});

test('handles edge cases for image size calculations', function () {
    $gallery = Gallery::factory()->create();
    $component = Livewire::test(GalleryImages::class, ['gallery' => $gallery->id]);

    // Test with non-existent indices
    expect($component->instance()->isImageOversized(999))->toBeFalse()
        ->and($component->instance()->getImageSizeInKB(999))->toBe(0);
});

test('stores images in correct directory structure', function () {
    $gallery = Gallery::factory()->create();
    $image = UploadedFile::fake()->image('test.jpg')->size(100);

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$image])
        ->call('store');

    $storedImage = GalleryImage::where('gallery_id', $gallery->id)->first();

    expect($storedImage->filename)->toStartWith('gallery-images/');
    Storage::disk('public')->assertExists($storedImage->filename);
});

test('clears images array after successful storage', function () {
    $gallery = Gallery::factory()->create();
    $image = UploadedFile::fake()->image('test.jpg')->size(100);

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$image])
        ->call('store')
        ->assertSet('images', []);
});

test('successfully updates image order with success message', function () {
    $gallery = Gallery::factory()->create();

    // Create some gallery images with specific orders
    $image1 = GalleryImage::factory()->create(['gallery_id' => $gallery->id, 'order' => 1]);
    $image2 = GalleryImage::factory()->create(['gallery_id' => $gallery->id, 'order' => 2]);
    $image3 = GalleryImage::factory()->create(['gallery_id' => $gallery->id, 'order' => 3]);

    $newOrder = [
        ['value' => $image3->id, 'order' => 1],
        ['value' => $image1->id, 'order' => 2],
        ['value' => $image2->id, 'order' => 3],
    ];

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->call('updateImageOrder', $newOrder)
        ->assertSessionHas('_flash.new.0', 'message');

    // Verify the order was updated in database
    expect(GalleryImage::find($image3->id)->order)->toBe(1)
        ->and(GalleryImage::find($image1->id)->order)->toBe(2)
        ->and(GalleryImage::find($image2->id)->order)->toBe(3);
});

test('validates maximum file size and shows custom error message', function () {
    $gallery = Gallery::factory()->create();
    $oversizedImage = UploadedFile::fake()->image('large.jpg')->size(600);

    Livewire::test(GalleryImages::class, ['gallery' => $gallery->id])
        ->set('images', [$oversizedImage])
        ->call('store')
        ->assertHasErrors(['images.*']);
});

test('maintains gallery relationship integrity during image operations', function () {
    $gallery1 = Gallery::factory()->create();
    $gallery2 = Gallery::factory()->create();

    $image1 = UploadedFile::fake()->image('image1.jpg')->size(100);
    $image2 = UploadedFile::fake()->image('image2.jpg')->size(150);

    // Store images for gallery1
    Livewire::test(GalleryImages::class, ['gallery' => $gallery1->id])
        ->set('images', [$image1, $image2])
        ->call('store');

    // Verify images belong to correct gallery
    expect(GalleryImage::where('gallery_id', $gallery1->id)->count())->toBe(2)
        ->and(GalleryImage::where('gallery_id', $gallery2->id)->count())->toBe(0);

    // Verify all images have correct gallery_id
    GalleryImage::where('gallery_id', $gallery1->id)->get()->each(function ($image) use ($gallery1) {
        expect($image->gallery_id)->toBe($gallery1->id);
    });
});
