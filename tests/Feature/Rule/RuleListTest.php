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
    $ruleA = Rule::factory()->create(['order' => 0, 'section' => 'house']);
    $ruleB = Rule::factory()->create(['order' => 1, 'section' => 'house']);

    // Move ruleB to position 0 (first)
    Livewire::test(RuleList::class)
        ->call('updateRuleOrder', (string) $ruleB->id, 0);

    $ruleA->refresh();
    $ruleB->refresh();

    expect($ruleB->order)->toBe(0)
        ->and($ruleA->order)->toBe(1);
});
