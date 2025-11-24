<?php

use App\Models\Newsletter;
use App\Services\NewsletterService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->newsletterService = new NewsletterService;
});

test('getAllSubscribers returns collection of all subscribers', function () {
    Newsletter::factory()->count(3)->create();

    $subscribers = $this->newsletterService->getAllSubscribers();

    expect($subscribers)->toBeInstanceOf(Collection::class)
        ->and($subscribers)->toHaveCount(3);
});

test('getAllSubscribers returns empty collection when no subscribers exist', function () {
    $subscribers = $this->newsletterService->getAllSubscribers();

    expect($subscribers)->toBeInstanceOf(Collection::class)
        ->and($subscribers)->toHaveCount(0);
});

test('getSubscriberById returns correct subscriber when exists', function () {
    $subscriber = Newsletter::factory()->create(['email' => 'test@example.com']);

    $foundSubscriber = $this->newsletterService->getSubscriberById($subscriber->id);

    expect($foundSubscriber)->not->toBeNull()
        ->and($foundSubscriber->id)->toBe($subscriber->id)
        ->and($foundSubscriber->email)->toBe('test@example.com');
});

test('getSubscriberById returns null when subscriber does not exist', function () {
    $nonExistentSubscriber = $this->newsletterService->getSubscriberById(999);

    expect($nonExistentSubscriber)->toBeNull();
});

test('deleteSubscriberById removes subscriber from database', function () {
    $subscriber = Newsletter::factory()->create(['email' => 'test@example.com']);

    $result = $this->newsletterService->deleteSubscriberById($subscriber);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('newsletters', ['id' => $subscriber->id]);
});

test('deleteAllSubscribers removes all subscribers from database', function () {
    Newsletter::factory()->count(3)->create();
    expect(Newsletter::count())->toBe(3);

    $this->newsletterService->deleteAllSubscribers();

    expect(Newsletter::count())->toBe(0);
});

test('deleteAllSubscribers does nothing when no subscribers exist', function () {
    expect(Newsletter::count())->toBe(0);

    $this->newsletterService->deleteAllSubscribers();

    expect(Newsletter::count())->toBe(0);
});

test('store creates new subscriber with provided data', function () {
    $subscriberData = ['email' => 'new@example.com'];

    $subscriber = $this->newsletterService->store($subscriberData);

    expect($subscriber)->toBeInstanceOf(Newsletter::class)
        ->and($subscriber->email)->toBe('new@example.com')
        ->and($subscriber->exists)->toBeTrue();

    $this->assertDatabaseHas('newsletters', [
        'id' => $subscriber->id,
        'email' => 'new@example.com',
    ]);
});

test('store sets created_at timestamp', function () {
    $subscriber = $this->newsletterService->store(['email' => 'test@example.com']);

    expect($subscriber->created_at)->not->toBeNull();
});

test('store handles multiple subscribers', function () {
    $this->newsletterService->store(['email' => 'first@example.com']);
    $this->newsletterService->store(['email' => 'second@example.com']);
    $this->newsletterService->store(['email' => 'third@example.com']);

    expect(Newsletter::count())->toBe(3);
    $this->assertDatabaseHas('newsletters', ['email' => 'first@example.com']);
    $this->assertDatabaseHas('newsletters', ['email' => 'second@example.com']);
    $this->assertDatabaseHas('newsletters', ['email' => 'third@example.com']);
});

test('store throws exception when duplicate email is provided', function () {
    Newsletter::factory()->create(['email' => 'duplicate@example.com']);

    expect(fn () => $this->newsletterService->store(['email' => 'duplicate@example.com']))
        ->toThrow(Exception::class);
});

test('getAllSubscribers returns subscribers ordered by created_at in descending order', function () {
    $oldestSubscriber = Newsletter::factory()->create(['created_at' => now()->subDays(2)]);
    $newestSubscriber = Newsletter::factory()->create(['created_at' => now()]);
    $middleSubscriber = Newsletter::factory()->create(['created_at' => now()->subDay()]);

    $subscribers = $this->newsletterService->getAllSubscribers();

    expect($subscribers[0]->id)->toBe($newestSubscriber->id)
        ->and($subscribers[1]->id)->toBe($middleSubscriber->id)
        ->and($subscribers[2]->id)->toBe($oldestSubscriber->id);
});

test('store validates required email field', function () {
    expect(fn () => $this->newsletterService->store([]))
        ->toThrow(Exception::class);
});

test('getSubscriberById rejects non-integer values', function () {
    expect(fn () => $this->newsletterService->getSubscriberById('string-id'))
        ->toThrow(TypeError::class);
});

test('deleteSubscriberById rejects non-Newsletter instances', function () {
    expect(fn () => $this->newsletterService->deleteSubscriberById(999))
        ->toThrow(TypeError::class);
});

test('getAllSubscribers includes all database columns', function () {
    $subscriber = Newsletter::factory()->create([
        'email' => 'test@example.com',
        'created_at' => now(),
    ]);

    $foundSubscriber = $this->newsletterService->getAllSubscribers()->first();

    expect($foundSubscriber->email)->toBe('test@example.com')
        ->and($foundSubscriber->created_at->toDateTimeString())->toBe($subscriber->created_at->toDateTimeString());
});

test('getSubscriberById with newly created subscriber returns correct data', function () {
    $subscriber = $this->newsletterService->store(['email' => 'fresh@example.com']);

    $retrievedSubscriber = $this->newsletterService->getSubscriberById($subscriber->id);

    expect($retrievedSubscriber->email)->toBe('fresh@example.com')
        ->and($retrievedSubscriber->id)->toBe($subscriber->id);
});
