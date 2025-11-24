<?php

use App\Livewire\Admin\Gallery\GalleryList;
use App\Models\Gallery;
use App\Models\User;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\GalleryServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');

    // Set up service mocks
    $this->galleryServices = $this->mock(GalleryServices::class);
    $this->flashMessageService = $this->mock(FlashMessageService::class);
    $this->errorLogService = $this->mock(ErrorLogService::class);

    // Bind mocks to container
    $this->app->instance(GalleryServices::class, $this->galleryServices);
    $this->app->instance(FlashMessageService::class, $this->flashMessageService);
    $this->app->instance(ErrorLogService::class, $this->errorLogService);
});

test('component renders successfully and displays all galleries regardless of status', function () {
    $galleries = collect([
        Gallery::factory()->create(['is_active' => true]),
        Gallery::factory()->create(['is_active' => true]),
        Gallery::factory()->create(['is_active' => true]),
        Gallery::factory()->create(['is_active' => false]),
        Gallery::factory()->create(['is_active' => false]),
    ]);

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn($galleries);

    // Add expectation for potential error logging (allow it but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->assertStatus(200)
        ->assertViewHas('galleries', function ($viewGalleries) {
            return $viewGalleries->count() === 5;
        });
});

test('toggles gallery status between active and inactive with success message', function () {
    $activeGallery = Gallery::factory()->create(['is_active' => true]);

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([$activeGallery]));

    $this->galleryServices->shouldReceive('toggleGalleryStatus')
        ->atLeast()->once()
        ->with($activeGallery->id)
        ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
        ->atLeast()->once()
        ->with('Galerijas statuss veiksmīgi atjaunināts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->call('toggleActive', $activeGallery->id)
        ->assertHasNoErrors();
});

test('handles toggle status for non-existent gallery with error message', function () {
    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([]));

    $this->galleryServices->shouldReceive('toggleGalleryStatus')
        ->atLeast()->once()
        ->with(999)
        ->andReturn(false);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Galerija nav atrasta vai nevarēja tikt atjaunināta.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->call('toggleActive', 999)
        ->assertHasNoErrors();
});

test('deletes gallery from database and shows success message', function () {
    $gallery = Gallery::factory()->create();

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([$gallery]));

    $this->galleryServices->shouldReceive('getGalleryById')
        ->atLeast()->once()
        ->with($gallery->id)
        ->andReturn($gallery);

    $this->galleryServices->shouldReceive('deleteGallery')
        ->atLeast()->once()
        ->with($gallery)
        ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
        ->atLeast()->once()
        ->with('Galerija veiksmīgi dzēsta.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->call('deleteGallery', $gallery->id)
        ->assertHasNoErrors();
});

test('handles gallery not found during deletion with error message', function () {
    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([]));

    $this->galleryServices->shouldReceive('getGalleryById')
        ->atLeast()->once()
        ->with(999)
        ->andReturn(null);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Galerija nav atrasta vai nevarēja tikt dzēsta.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->call('deleteGallery', 999)
        ->assertHasNoErrors();
});

test('handles error during gallery loading', function () {
    $exception = new \Exception('Database error');

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andThrow($exception);

    $this->errorLogService->shouldReceive('logError')
        ->atLeast()->once()
        ->with('Failed to load galleries list', $exception, []);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Ielādējot galerijas, radās kļūda. Lūdzu, atsvaidziniet lapu.');

    Livewire::test(GalleryList::class)
        ->assertViewHas('galleries', function ($galleries) {
            return $galleries->isEmpty();
        });
});

test('deletes gallery with images', function () {
    $gallery = Gallery::factory()->create();
    $gallery->images()->createMany([
        ['filename' => 'gallery-images/image1.jpg', 'order' => 1],
        ['filename' => 'gallery-images/image2.jpg', 'order' => 2],
    ]);

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([$gallery]));

    $this->galleryServices->shouldReceive('getGalleryById')
        ->atLeast()->once()
        ->with($gallery->id)
        ->andReturn($gallery);

    $this->galleryServices->shouldReceive('deleteGallery')
        ->atLeast()->once()
        ->with($gallery)
        ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
        ->atLeast()->once()
        ->with('Galerija veiksmīgi dzēsta.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->call('deleteGallery', $gallery->id)
        ->assertHasNoErrors();
});

test('refreshes gallery list after performing actions', function () {
    $galleries = collect([
        Gallery::factory()->create(['is_active' => true]),
        Gallery::factory()->create(['is_active' => true]),
        Gallery::factory()->create(['is_active' => false]),
    ]);

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->times(3)
        ->andReturn($galleries, $galleries, $galleries->slice(0, 2));

    $this->galleryServices->shouldReceive('toggleGalleryStatus')
        ->atLeast()->once()
        ->with($galleries->first()->id)
        ->andReturn(true);

    $this->galleryServices->shouldReceive('getGalleryById')
        ->atLeast()->once()
        ->with($galleries->last()->id)
        ->andReturn($galleries->last());

    $this->galleryServices->shouldReceive('deleteGallery')
        ->atLeast()->once()
        ->with($galleries->last())
        ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
        ->times(2);

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    $component = Livewire::test(GalleryList::class);

    // Toggle status of a gallery
    $component->call('toggleActive', $galleries->first()->id);

    // Delete a gallery
    $component->call('deleteGallery', $galleries->last()->id);
});

test('handles error during gallery deletion', function () {
    $gallery = Gallery::factory()->create();
    $exception = new \Exception('Deletion failed');

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([$gallery]));

    $this->galleryServices->shouldReceive('getGalleryById')
        ->atLeast()->once()
        ->with($gallery->id)
        ->andReturn($gallery);

    $this->galleryServices->shouldReceive('deleteGallery')
        ->atLeast()->once()
        ->with($gallery)
        ->andThrow($exception);

    $this->errorLogService->shouldReceive('logError')
        ->atLeast()->once()
        ->with('Failed to delete gallery', $exception, ['gallery_id' => $gallery->id]);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Dzēšot galeriju, radās kļūda. Lūdzu, mēģiniet vēlreiz.');

    Livewire::test(GalleryList::class)
        ->call('deleteGallery', $gallery->id)
        ->assertHasNoErrors();
});

test('handles error during gallery status toggle', function () {
    $gallery = Gallery::factory()->create();
    $exception = new \Exception('Toggle failed');

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([$gallery]));

    $this->galleryServices->shouldReceive('toggleGalleryStatus')
        ->atLeast()->once()
        ->with($gallery->id)
        ->andThrow($exception);

    $this->errorLogService->shouldReceive('logError')
        ->atLeast()->once()
        ->with('Failed to toggle gallery status', $exception, ['gallery_id' => $gallery->id]);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Atjauninot galerijas statusu, radās kļūda. Lūdzu, mēģiniet vēlreiz.');

    Livewire::test(GalleryList::class)
        ->call('toggleActive', $gallery->id)
        ->assertHasNoErrors();
});

test('displays galleries ordered by order column', function () {
    $gallery1 = Gallery::factory()->create(['order' => 3]);
    $gallery2 = Gallery::factory()->create(['order' => 1]);
    $gallery3 = Gallery::factory()->create(['order' => 2]);

    $orderedGalleries = collect([$gallery2, $gallery3, $gallery1]); // Ordered by order column

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn($orderedGalleries);

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->assertViewHas('galleries', function ($viewGalleries) {
            return $viewGalleries->count() === 3 &&
                   $viewGalleries->first()->order === 1 &&
                   $viewGalleries->last()->order === 3;
        });
});

test('component handles empty gallery collection', function () {
    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([]));

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->assertStatus(200)
        ->assertViewHas('galleries', function ($galleries) {
            return $galleries->isEmpty();
        });
});

test('updates individual gallery order in sequence', function () {
    $gallery1 = Gallery::factory()->create(['order' => 1]);
    $gallery2 = Gallery::factory()->create(['order' => 2]);
    $gallery3 = Gallery::factory()->create(['order' => 3]);

    $orderData = [
        ['value' => $gallery3->id, 'order' => 1],
        ['value' => $gallery1->id, 'order' => 2],
        ['value' => $gallery2->id, 'order' => 3],
    ];

    $this->galleryServices->shouldReceive('getAllGalleries')
        ->atLeast()->once()
        ->andReturn(collect([]));

    $this->flashMessageService->shouldReceive('success')
        ->atLeast()->once()
        ->with('Secība atjaunota.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(GalleryList::class)
        ->call('updateGalleryOrder', $orderData)
        ->assertHasNoErrors();

    // Verify order was updated in database
    expect(Gallery::find($gallery3->id)->order)->toBe(1)
        ->and(Gallery::find($gallery1->id)->order)->toBe(2)
        ->and(Gallery::find($gallery2->id)->order)->toBe(3);
});
