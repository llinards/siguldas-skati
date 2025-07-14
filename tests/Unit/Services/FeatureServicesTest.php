<?php

use App\Models\Feature;
use App\Services\FeatureService;
use App\Services\FileStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock the FileStorageService dependency
    $this->fileStorageService = Mockery::mock(FileStorageService::class);
    $this->featureService = new FeatureService($this->fileStorageService);
    Storage::fake('public');
});

test('getAllFeatures returns all features ordered by order column', function () {
    Feature::factory()->create(['order' => 3, 'title' => 'Third Feature']);
    Feature::factory()->create(['order' => 1, 'title' => 'First Feature']);
    Feature::factory()->create(['order' => 2, 'title' => 'Second Feature']);

    $features = $this->featureService->getAllFeatures();

    expect($features)->toHaveCount(3)
        ->and($features->first()->order)->toBe(1)
        ->and($features->last()->order)->toBe(3);
});

test('getFeatureById returns existing feature', function () {
    $feature = Feature::factory()->create(['title' => 'Test Feature']);

    $result = $this->featureService->getFeatureById($feature->id);

    expect($result)->toBeInstanceOf(Feature::class)
        ->and($result->id)->toBe($feature->id)
        ->and($result->title)->toBe('Test Feature');
});

test('getFeatureById returns null for non-existent feature', function () {
    expect($this->featureService->getFeatureById(999))->toBeNull();
});

test('createFeature successfully creates a feature with provided data', function () {
    $featureData = [
        'title' => 'New Feature',
        'is_active' => true,
        'order' => 1,
    ];

    $feature = $this->featureService->createFeature($featureData);

    expect($feature)->toBeInstanceOf(Feature::class)
        ->and($feature->title)->toBe('New Feature')
        ->and($feature->is_active)->toBeTrue()
        ->and($feature->order)->toBe(1);
});

test('updateFeature successfully updates an existing feature', function () {
    $feature = Feature::factory()->create([
        'title' => 'Original Feature',
        'is_active' => false,
    ]);

    $updateData = [
        'title' => 'Updated Feature',
        'is_active' => true,
    ];

    $result = $this->featureService->updateFeature($feature, $updateData);

    expect($result)->toBeTrue()
        ->and($feature->fresh()->title)->toBe('Updated Feature')
        ->and($feature->fresh()->is_active)->toBeTrue();
});

test('deleteFeature removes feature with icon image', function () {
    $feature = Feature::factory()->create(['icon_image' => 'test-icon.jpg']);

    // Mock the deleteFile method to be called
    $this->fileStorageService->shouldReceive('deleteFile')
        ->once()
        ->with('test-icon.jpg');

    $result = $this->featureService->deleteFeature($feature);

    expect($result)->toBeTrue()
        ->and(Feature::find($feature->id))->toBeNull();
});

test('toggleFeatureStatus switches between active and inactive states', function () {
    $activeFeature = Feature::factory()->create(['is_active' => true]);
    $inactiveFeature = Feature::factory()->create(['is_active' => false]);

    // Test deactivating active feature
    $result1 = $this->featureService->toggleFeatureStatus($activeFeature->id);
    expect($result1)->toBeTrue()
        ->and($activeFeature->fresh()->is_active)->toBeFalse();

    // Test activating inactive feature
    $result2 = $this->featureService->toggleFeatureStatus($inactiveFeature->id);
    expect($result2)->toBeTrue()
        ->and($inactiveFeature->fresh()->is_active)->toBeTrue();
});

test('toggleFeatureStatus returns false for non-existent feature', function () {
    expect($this->featureService->toggleFeatureStatus(999))->toBeFalse();
});

test('updateFeatureOrder successfully updates feature order values', function () {
    $feature1 = Feature::factory()->create(['order' => 1]);
    $feature2 = Feature::factory()->create(['order' => 2]);
    $feature3 = Feature::factory()->create(['order' => 3]);

    $orderData = [
        ['value' => $feature1->id, 'order' => 3],
        ['value' => $feature2->id, 'order' => 1],
        ['value' => $feature3->id, 'order' => 2],
    ];

    $this->featureService->updateFeatureOrder($orderData);

    expect($feature1->fresh()->order)->toBe(3)
        ->and($feature2->fresh()->order)->toBe(1)
        ->and($feature3->fresh()->order)->toBe(2);
});

test('returns empty collection when no features exist', function () {
    expect($this->featureService->getAllFeatures())->toBeEmpty();
});

test('storeFeatureIcon calls fileStorageService with correct parameters', function () {
    $uploadedFile = \Illuminate\Http\Testing\File::fake()->image('icon.jpg');

    $this->fileStorageService->shouldReceive('storeFile')
        ->once()
        ->with($uploadedFile, FileStorageService::FEATURE_ICON_PATH)
        ->andReturn('stored-icon.jpg');

    $result = $this->featureService->storeFeatureIcon($uploadedFile);

    expect($result)->toBe('stored-icon.jpg');
});

test('updateFeatureIcon deletes old icon and stores new one', function () {
    $feature = Feature::factory()->create(['icon_image' => 'old-icon.jpg']);
    $uploadedFile = \Illuminate\Http\Testing\File::fake()->image('new-icon.jpg');

    $this->fileStorageService->shouldReceive('deleteFile')
        ->once()
        ->with('old-icon.jpg');

    $this->fileStorageService->shouldReceive('storeFile')
        ->once()
        ->with($uploadedFile, FileStorageService::FEATURE_ICON_PATH)
        ->andReturn('new-stored-icon.jpg');

    $result = $this->featureService->updateFeatureIcon($feature, $uploadedFile);

    expect($result)->toBe('new-stored-icon.jpg');
});
