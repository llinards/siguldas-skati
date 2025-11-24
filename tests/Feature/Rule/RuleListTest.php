<?php

use App\Livewire\Admin\Rule\RuleList;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('component renders successfully', function () {
    // Seed a couple of rules to ensure list loads
    Rule::factory()->count(3)->create();

    Livewire::test(RuleList::class)
        ->assertStatus(200);
});

it('toggles rule active status', function () {
    $rule = Rule::factory()->create([
        'is_active' => true,
        'order' => 1,
    ]);

    Livewire::test(RuleList::class)
        ->call('toggleActive', $rule->id);

    $rule->refresh();
    expect($rule->is_active)->toBeFalse();

    // Toggle back to active
    Livewire::test(RuleList::class)
        ->call('toggleActive', $rule->id);

    $rule->refresh();
    expect($rule->is_active)->toBeTrue();
});

it('deletes a rule', function () {
    $rule = Rule::factory()->create([
        'is_active' => true,
        'order' => 1,
    ]);

    expect(Rule::count())->toBe(1);

    Livewire::test(RuleList::class)
        ->call('deleteRule', $rule->id);

    expect(Rule::count())->toBe(0);
});

it('updates rule order', function () {
    $ruleA = Rule::factory()->create(['order' => 1]);
    $ruleB = Rule::factory()->create(['order' => 2]);

    // Swap their order using the expected payload shape
    $payload = [
        ['value' => $ruleA->id, 'order' => 2],
        ['value' => $ruleB->id, 'order' => 1],
    ];

    Livewire::test(RuleList::class)
        ->call('updateRuleOrder', $payload);

    $ruleA->refresh();
    $ruleB->refresh();

    expect($ruleA->order)->toBe(2)
        ->and($ruleB->order)->toBe(1);
});
