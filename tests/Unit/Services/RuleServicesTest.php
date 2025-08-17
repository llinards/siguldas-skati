<?php

use App\Models\Rule;
use App\Services\FileStorageService;
use App\Services\RuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock the FileStorageService dependency
    $this->fileStorageService = Mockery::mock(FileStorageService::class);
    $this->ruleService = new RuleService($this->fileStorageService);
    Storage::fake('public');
});

test('getAllRules returns all rules ordered by order column', function () {
    Rule::factory()->create(['order' => 3, 'title' => 'Third Rule']);
    Rule::factory()->create(['order' => 1, 'title' => 'First Rule']);
    Rule::factory()->create(['order' => 2, 'title' => 'Second Rule']);

    $rules = $this->ruleService->getAllRules();

    expect($rules)->toHaveCount(3)
        ->and($rules->first()->order)->toBe(1)
        ->and($rules->last()->order)->toBe(3);
});

test('getRuleById returns existing rule', function () {
    $rule = Rule::factory()->create(['title' => 'Test Rule']);

    $result = $this->ruleService->getRuleById($rule->id);

    expect($result)->toBeInstanceOf(Rule::class)
        ->and($result->id)->toBe($rule->id)
        ->and($result->title)->toBe('Test Rule');
});

test('getRuleById returns null for non-existent rule', function () {
    expect($this->ruleService->getRuleById(999))->toBeNull();
});

test('createRule successfully creates a rule with provided data', function () {
    $ruleData = [
        'title' => 'New Rule',
        'is_active' => true,
        'order' => 1,
        'section' => Rule::SECTION_HOUSE,
    ];

    $rule = $this->ruleService->createRule($ruleData);

    expect($rule)->toBeInstanceOf(Rule::class)
        ->and($rule->title)->toBe('New Rule')
        ->and($rule->is_active)->toBeTrue()
        ->and($rule->order)->toBe(1)
        ->and($rule->section)->toBe(Rule::SECTION_HOUSE);
});

test('updateRule successfully updates an existing rule', function () {
    $rule = Rule::factory()->create([
        'title' => 'Original Rule',
        'is_active' => false,
        'section' => Rule::SECTION_HOUSE,
    ]);

    $updateData = [
        'title' => 'Updated Rule',
        'is_active' => true,
        'section' => Rule::SECTION_SAFETY,
    ];

    $result = $this->ruleService->updateRule($rule, $updateData);

    expect($result)->toBeTrue()
        ->and($rule->fresh()->title)->toBe('Updated Rule')
        ->and($rule->fresh()->is_active)->toBeTrue()
        ->and($rule->fresh()->section)->toBe(Rule::SECTION_SAFETY);
});

test('deleteRule removes rule with icon image', function () {
    $rule = Rule::factory()->create(['icon_image' => 'test-icon.jpg']);

    // Mock the deleteFile method to be called
    $this->fileStorageService->shouldReceive('deleteFile')
        ->once()
        ->with('test-icon.jpg');

    $result = $this->ruleService->deleteRule($rule);

    expect($result)->toBeTrue()
        ->and(Rule::find($rule->id))->toBeNull();
});

test('toggleRuleStatus switches between active and inactive states', function () {
    $activeRule = Rule::factory()->create(['is_active' => true]);
    $inactiveRule = Rule::factory()->create(['is_active' => false]);

    // Test deactivating active rule
    $result1 = $this->ruleService->toggleRuleStatus($activeRule->id);
    expect($result1)->toBeTrue()
        ->and($activeRule->fresh()->is_active)->toBeFalse();

    // Test activating inactive rule
    $result2 = $this->ruleService->toggleRuleStatus($inactiveRule->id);
    expect($result2)->toBeTrue()
        ->and($inactiveRule->fresh()->is_active)->toBeTrue();
});

test('toggleRuleStatus returns false for non-existent rule', function () {
    expect($this->ruleService->toggleRuleStatus(999))->toBeFalse();
});

test('updateRuleOrder successfully updates rule order values', function () {
    $rule1 = Rule::factory()->create(['order' => 1]);
    $rule2 = Rule::factory()->create(['order' => 2]);
    $rule3 = Rule::factory()->create(['order' => 3]);

    $orderData = [
        ['value' => $rule1->id, 'order' => 3],
        ['value' => $rule2->id, 'order' => 1],
        ['value' => $rule3->id, 'order' => 2],
    ];

    $this->ruleService->updateRuleOrder($orderData);

    expect($rule1->fresh()->order)->toBe(3)
        ->and($rule2->fresh()->order)->toBe(1)
        ->and($rule3->fresh()->order)->toBe(2);
});

test('returns empty collection when no rules exist', function () {
    expect($this->ruleService->getAllRules())->toBeEmpty();
});

test('storeRuleIcon calls fileStorageService with correct parameters', function () {
    $uploadedFile = \Illuminate\Http\Testing\File::fake()->image('icon.jpg');

    $this->fileStorageService->shouldReceive('storeFile')
        ->once()
        ->with($uploadedFile, FileStorageService::FEATURE_ICON_PATH)
        ->andReturn('stored-icon.jpg');

    $result = $this->ruleService->storeRuleIcon($uploadedFile);

    expect($result)->toBe('stored-icon.jpg');
});

test('updateRuleIcon deletes old icon and stores new one', function () {
    $rule = Rule::factory()->create(['icon_image' => 'old-icon.jpg']);
    $uploadedFile = \Illuminate\Http\Testing\File::fake()->image('new-icon.jpg');

    $this->fileStorageService->shouldReceive('deleteFile')
        ->once()
        ->with('old-icon.jpg');

    $this->fileStorageService->shouldReceive('storeFile')
        ->once()
        ->with($uploadedFile, FileStorageService::FEATURE_ICON_PATH)
        ->andReturn('new-stored-icon.jpg');

    $result = $this->ruleService->updateRuleIcon($rule, $uploadedFile);

    expect($result)->toBe('new-stored-icon.jpg');
});
