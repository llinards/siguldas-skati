<?php

use App\Livewire\Admin\HeaderMedia as HeaderMediaComponent;
use App\Models\HeaderMedia;
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

    Livewire::test(HeaderMediaComponent::class)
        ->assertStatus(200)
        ->assertSet('media', []);
});

test('stores uploaded images to storage and database with sequential order', function () {
    $this->actingAs($this->user);
    $image1 = UploadedFile::fake()->image('one.jpg')->size(100);
    $image2 = UploadedFile::fake()->image('two.jpg')->size(150);

    Livewire::test(HeaderMediaComponent::class)
        ->set('media', [$image1, $image2])
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('media', []);

    expect(HeaderMedia::count())->toBe(2);

    HeaderMedia::all()->each(function (HeaderMedia $item) {
        Storage::disk('public')->assertExists($item->filename);
        expect($item->filename)->toStartWith('header-images/');
        expect($item->type)->toBe(HeaderMedia::TYPE_IMAGE);
    });

    $orders = HeaderMedia::withoutGlobalScope('order')->pluck('order')->sort()->values()->all();
    expect($orders)->toBe([0, 1]);
});

test('stores an uploaded mp4 video with type=video under the videos folder', function () {
    $this->actingAs($this->user);
    $video = UploadedFile::fake()->create('clip.mp4', 5000, 'video/mp4'); // 5 MB

    Livewire::test(HeaderMediaComponent::class)
        ->set('media', [$video])
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('media', []);

    $stored = HeaderMedia::firstOrFail();
    expect($stored->type)->toBe(HeaderMedia::TYPE_VIDEO)
        ->and($stored->filename)->toStartWith('header-videos/');
    Storage::disk('public')->assertExists($stored->filename);
});

test('validates required media field', function () {
    $this->actingAs($this->user);

    Livewire::test(HeaderMediaComponent::class)
        ->set('media', [])
        ->call('store')
        ->assertHasErrors(['media' => 'required']);
});

test('rejects images exceeding the 512 KB size cap', function () {
    $this->actingAs($this->user);
    $oversized = UploadedFile::fake()->image('big.jpg')->size(600);

    Livewire::test(HeaderMediaComponent::class)
        ->set('media', [$oversized])
        ->call('store')
        ->assertHasErrors(['media.*']);
});

test('rejects videos exceeding the 15 MB size cap', function () {
    $this->actingAs($this->user);
    $oversizedVideo = UploadedFile::fake()->create('huge.mp4', 16000, 'video/mp4'); // 16 MB

    Livewire::test(HeaderMediaComponent::class)
        ->set('media', [$oversizedVideo])
        ->call('store')
        ->assertHasErrors(['media.*']);
});

test('rejects non-mp4 video formats', function () {
    $this->actingAs($this->user);
    $movFile = UploadedFile::fake()->create('clip.mov', 2000, 'video/quicktime'); // 2 MB

    Livewire::test(HeaderMediaComponent::class)
        ->set('media', [$movFile])
        ->call('store')
        ->assertHasErrors(['media.*']);
});

test('removes an existing header media item from storage and database', function () {
    $this->actingAs($this->user);

    $path = 'header-images/test.jpg';
    Storage::disk('public')->put($path, 'fake-bytes');
    $item = HeaderMedia::factory()->create(['filename' => $path, 'order' => 0]);

    Livewire::test(HeaderMediaComponent::class)
        ->call('removeMedia', $item->id);

    Storage::disk('public')->assertMissing($path);
    expect(HeaderMedia::find($item->id))->toBeNull();
});

test('reorders media via updateMediaOrder across types', function () {
    $this->actingAs($this->user);

    $a = HeaderMedia::factory()->image()->create(['order' => 0]);
    $b = HeaderMedia::factory()->video()->create(['order' => 1]);
    $c = HeaderMedia::factory()->image()->create(['order' => 2]);

    Livewire::test(HeaderMediaComponent::class)
        ->call('updateMediaOrder', (string) $c->id, 0);

    expect(HeaderMedia::find($c->id)->order)->toBe(0)
        ->and(HeaderMedia::find($a->id)->order)->toBe(1)
        ->and(HeaderMedia::find($b->id)->order)->toBe(2);
});

test('home page passes ordered header media to the view', function () {
    HeaderMedia::factory()->video()->create(['filename' => 'header-videos/second.mp4', 'order' => 1]);
    HeaderMedia::factory()->image()->create(['filename' => 'header-images/first.jpg', 'order' => 0]);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    $headerMedia = $response->viewData('headerMedia');

    expect($headerMedia)->toHaveCount(2)
        ->and($headerMedia->pluck('filename')->all())
        ->toBe(['header-images/first.jpg', 'header-videos/second.mp4'])
        ->and($headerMedia->pluck('type')->all())
        ->toBe([HeaderMedia::TYPE_IMAGE, HeaderMedia::TYPE_VIDEO]);
});

test('home page renders a <video> tag for video rows and <img> for image rows', function () {
    HeaderMedia::factory()->image()->create(['filename' => 'header-images/first.jpg', 'order' => 0]);
    HeaderMedia::factory()->video()->create(['filename' => 'header-videos/second.mp4', 'order' => 1]);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    $html = $response->getContent();

    expect($html)->toContain('<img src="/storage/header-images/first.jpg"')
        ->and($html)->toContain('<video src="/storage/header-videos/second.mp4"');
});

test('home page renders without errors when there are no header media items', function () {
    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->viewData('headerMedia')->isEmpty())->toBeTrue();
});

test('single video gets the loop attribute', function () {
    HeaderMedia::factory()->video()->create(['filename' => 'header-videos/only.mp4', 'order' => 0]);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())->toMatch('/<video[^>]*\sloop\b[^>]*>/');
});

test('multiple slides do not put loop on the video', function () {
    HeaderMedia::factory()->image()->create(['filename' => 'header-images/a.jpg', 'order' => 0]);
    HeaderMedia::factory()->video()->create(['filename' => 'header-videos/b.mp4', 'order' => 1]);

    $response = $this->get('/lv');

    $response->assertStatus(200);
    expect($response->getContent())->not->toMatch('/<video[^>]*\sloop\b/');
});
