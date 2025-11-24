<?php

use App\Livewire\Admin\Gallery\AddGallery;
use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('component renders successfully without errors', function () {
    Livewire::test(AddGallery::class)
        ->assertStatus(200);
});

test('prevents submission when required fields are missing and displays validation errors', function () {
    Livewire::test(AddGallery::class)
        ->set('title', '')
        ->call('store')
        ->assertHasErrors(['title']);
});

test('successfully creates gallery with localized title and stores data correctly', function () {
    Livewire::test(AddGallery::class)
        ->set('title', 'Jauna Galerija')
        ->set('is_active', true)
        ->call('store')
        ->assertSessionHas('_flash.new.0', 'message')
        ->assertRedirect(route('dashboard.galleries'));

    // Check that gallery was created with localized data
    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['lv' => 'Jauna Galerija']),
        'is_active' => 1,
    ]);

    // Verify that gallery was actually created
    $gallery = Gallery::where('title->lv', 'Jauna Galerija')->first();
    expect($gallery)->not->toBeNull()
        ->and($gallery->is_active)->toBeTrue();
});

test('creates gallery with inactive status when is_active is false', function () {
    Livewire::test(AddGallery::class)
        ->set('title', 'Neaktiva Galerija')
        ->set('is_active', false)
        ->call('store')
        ->assertSessionHas('_flash.new.0', 'message')
        ->assertRedirect(route('dashboard.galleries'));

    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['lv' => 'Neaktiva Galerija']),
        'is_active' => 0,
    ]);
});

test('creates gallery with default inactive status when is_active is not set', function () {
    Livewire::test(AddGallery::class)
        ->set('title', 'Noklusejuma Galerija')
        ->call('store')
        ->assertSessionHas('_flash.new.0', 'message')
        ->assertRedirect(route('dashboard.galleries'));

    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['lv' => 'Noklusejuma Galerija']),
        'is_active' => 0,
    ]);
});

test('generates proper localized title data across different locales', function () {
    // Test Latvian locale
    app()->setLocale('lv');
    Livewire::test(AddGallery::class)
        ->set('title', 'Latviesu Galerija')
        ->set('is_active', true)
        ->call('store');

    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['lv' => 'Latviesu Galerija']),
    ]);

    // Clear database for next test
    Gallery::truncate();

    // Test English locale
    app()->setLocale('en');
    Livewire::test(AddGallery::class)
        ->set('title', 'English Gallery')
        ->set('is_active', true)
        ->call('store');

    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['en' => 'English Gallery']),
    ]);
});

test('validates that title cannot be empty string', function () {
    Livewire::test(AddGallery::class)
        ->set('title', '   ')
        ->call('store')
        ->assertHasErrors(['title']);

    // Ensure no gallery was created
    expect(Gallery::count())->toBe(0);
});

test('creates multiple galleries with different titles successfully', function () {
    // Create first gallery
    Livewire::test(AddGallery::class)
        ->set('title', 'Pirma Galerija')
        ->set('is_active', true)
        ->call('store');

    // Create second gallery
    Livewire::test(AddGallery::class)
        ->set('title', 'Otra Galerija')
        ->set('is_active', false)
        ->call('store');

    // Verify both galleries exist
    expect(Gallery::count())->toBe(2);

    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['lv' => 'Pirma Galerija']),
        'is_active' => 1,
    ]);

    $this->assertDatabaseHas('galleries', [
        'title' => json_encode(['lv' => 'Otra Galerija']),
        'is_active' => 0,
    ]);
});

test('redirects to correct route after successful creation', function () {
    Livewire::test(AddGallery::class)
        ->set('title', 'Test Redirect')
        ->call('store')
        ->assertRedirect(route('dashboard.galleries'));
});

test('displays success message after gallery creation', function () {
    Livewire::test(AddGallery::class)
        ->set('title', 'Success Test')
        ->set('is_active', true)
        ->call('store')
        ->assertSessionHas('_flash.new.0', 'message');
});
