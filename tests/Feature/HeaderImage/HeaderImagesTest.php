<?php

use App\Livewire\Admin\HeaderImages;
use App\Models\HeaderImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('public');
});

test('component renders successfully for an authenticated user', function () {
    $this->actingAs($this->user);

    Livewire::test(HeaderImages::class)
        ->assertStatus(200)
        ->assertSet('images', []);
});

test('stores uploaded images to storage and database with sequential order', function () {
    $this->actingAs($this->user);
    $image1 = UploadedFile::fake()->image('one.jpg')->size(100);
    $image2 = UploadedFile::fake()->image('two.jpg')->size(150);

    Livewire::test(HeaderImages::class)
        ->set('images', [$image1, $image2])
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('images', []);

    expect(HeaderImage::count())->toBe(2);

    HeaderImage::all()->each(function (HeaderImage $image) {
        Storage::disk('public')->assertExists($image->filename);
        expect($image->filename)->toStartWith('header-images/');
    });

    $orders = HeaderImage::withoutGlobalScope('order')->pluck('order')->sort()->values()->all();
    expect($orders)->toBe([0, 1]);
});

test('validates required images field', function () {
    $this->actingAs($this->user);

    Livewire::test(HeaderImages::class)
        ->set('images', [])
        ->call('store')
        ->assertHasErrors(['images' => 'required']);
});

test('rejects images exceeding the size cap on store', function () {
    $this->actingAs($this->user);
    $oversized = UploadedFile::fake()->image('big.jpg')->size(600);

    Livewire::test(HeaderImages::class)
        ->set('images', [$oversized])
        ->call('store')
        ->assertHasErrors(['images.*']);
});

test('removes an existing header image from storage and database', function () {
    $this->actingAs($this->user);

    $path = 'header-images/test.jpg';
    Storage::disk('public')->put($path, 'fake-bytes');
    $headerImage = HeaderImage::factory()->create(['filename' => $path, 'order' => 0]);

    Livewire::test(HeaderImages::class)
        ->call('removeImage', $headerImage->id);

    Storage::disk('public')->assertMissing($path);
    expect(HeaderImage::find($headerImage->id))->toBeNull();
});

test('reorders images via updateImageOrder', function () {
    $this->actingAs($this->user);

    $a = HeaderImage::factory()->create(['order' => 0]);
    $b = HeaderImage::factory()->create(['order' => 1]);
    $c = HeaderImage::factory()->create(['order' => 2]);

    Livewire::test(HeaderImages::class)
        ->call('updateImageOrder', (string) $c->id, 0);

    expect(HeaderImage::find($c->id)->order)->toBe(0)
        ->and(HeaderImage::find($a->id)->order)->toBe(1)
        ->and(HeaderImage::find($b->id)->order)->toBe(2);
});

test('home page passes ordered header images to the view', function () {
    HeaderImage::factory()->create(['filename' => 'header-images/second.jpg', 'order' => 1]);
    HeaderImage::factory()->create(['filename' => 'header-images/first.jpg', 'order' => 0]);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    $headerImages = $response->viewData('headerImages');

    expect($headerImages)->toHaveCount(2)
        ->and($headerImages->pluck('filename')->all())
        ->toBe(['header-images/first.jpg', 'header-images/second.jpg']);
});

test('home page renders without errors when there are no header images', function () {
    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->viewData('headerImages')->isEmpty())->toBeTrue();
});
