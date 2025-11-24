
<?php

use App\Livewire\Admin\Gallery\EditGallery;
use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully when mounted with existing gallery', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['en' => 'Test Gallery', 'lv' => 'Testa Galerija'],
        'is_active' => true,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->assertStatus(200)
        ->assertSet('title', 'Testa Galerija')
        ->assertSet('is_active', true);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    $gallery = Gallery::factory()->create();

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', '')
        ->call('update')
        ->assertHasErrors(['title']);
});

test('successfully updates gallery with new data and shows success message', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['en' => 'Old Title', 'lv' => 'Vecais Nosaukums'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Jaunais Nosaukums')
        ->set('is_active', true)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message')
        ->assertRedirect(route('dashboard.galleries'));

    $gallery->refresh();
    expect($gallery->title)->toBe('Jaunais Nosaukums')
        ->and($gallery->is_active)->toBeTrue();
});

test('updates gallery title across different locales correctly', function () {
    // Test Latvian locale with special characters
    app()->setLocale('lv');
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Vecais Nosaukums'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Galerija ar Latviešu Simboliem')
        ->set('is_active', true)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->title)->toBe('Galerija ar Latviešu Simboliem')
        ->and($gallery->is_active)->toBeTrue();

    // Test English locale
    app()->setLocale('en');
    $englishGallery = Gallery::factory()->create([
        'title' => ['en' => 'Old English Title'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $englishGallery])
        ->set('title', 'New Gallery with Symbols')
        ->set('is_active', true)
        ->call('update');

    $englishGallery->refresh();
    expect($englishGallery->title)->toBe('New Gallery with Symbols')
        ->and($englishGallery->is_active)->toBeTrue();
});

test('handles special characters in gallery title correctly during update', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Vecais Nosaukums'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Galerija ar īpašajām rakstzīmēm: āēīōū')
        ->set('is_active', true)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->title)->toBe('Galerija ar īpašajām rakstzīmēm: āēīōū')
        ->and($gallery->is_active)->toBeTrue();
});

test('validates that title cannot be empty string during update', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Existing Title'],
        'is_active' => true,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', '   ')
        ->call('update')
        ->assertHasErrors(['title']);

    // Ensure gallery data remains unchanged
    $gallery->refresh();
    expect($gallery->title)->toBe('Existing Title');
});

test('toggles gallery status from active to inactive', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Active Gallery'],
        'is_active' => true,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Active Gallery')
        ->set('is_active', false)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->is_active)->toBeFalse();
});

test('toggles gallery status from inactive to active', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Inactive Gallery'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Inactive Gallery')
        ->set('is_active', true)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->is_active)->toBeTrue();
});

test('updates only title without changing status', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Old Title'],
        'is_active' => true,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'New Title')
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->title)->toBe('New Title')
        ->and($gallery->is_active)->toBeTrue(); // Status remains unchanged
});

test('updates only status without changing title', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Unchanged Title'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('is_active', true)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->title)->toBe('Unchanged Title') // Title remains unchanged
        ->and($gallery->is_active)->toBeTrue();
});

test('redirects to correct route after successful update', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Test Gallery'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Updated Gallery')
        ->call('update')
        ->assertRedirect(route('dashboard.galleries'));
});

test('displays success message after gallery update', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Success Test'],
        'is_active' => false,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Updated Success Test')
        ->set('is_active', true)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');
});

test('handles gallery with existing images during update', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Gallery with Images'],
        'is_active' => true,
    ]);

    // Create some images for the gallery
    $gallery->images()->createMany([
        ['filename' => 'image1.jpg', 'order' => 1],
        ['filename' => 'image2.jpg', 'order' => 2],
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Updated Gallery with Images')
        ->set('is_active', false)
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->title)->toBe('Updated Gallery with Images')
        ->and($gallery->is_active)->toBeFalse()
        ->and($gallery->images)->toHaveCount(2); // Images should remain intact
});

test('preserves gallery order during update', function () {
    $gallery = Gallery::factory()->create([
        'title' => ['lv' => 'Ordered Gallery'],
        'is_active' => true,
        'order' => 5,
    ]);

    Livewire::test(EditGallery::class, ['gallery' => $gallery])
        ->set('title', 'Updated Ordered Gallery')
        ->call('update')
        ->assertSessionHas('_flash.new.0', 'message');

    $gallery->refresh();
    expect($gallery->title)->toBe('Updated Ordered Gallery')
        ->and($gallery->order)->toBe(5); // Order should remain unchanged
});
