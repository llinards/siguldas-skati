<?php

//
// use App\Models\Newsletter;
// use App\Services\NewsletterService;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Foundation\Testing\RefreshDatabase;
//
// uses(RefreshDatabase::class);
//
// beforeEach(function () {
//    $this->service = new NewsletterService;
// });
//
// it('can get all subscribers', function () {
//    Newsletter::factory()->create(['email' => 'test1@example.com']);
//    Newsletter::factory()->create(['email' => 'test2@example.com']);
//    Newsletter::factory()->create(['email' => 'test3@example.com']);
//
//    $result = $this->service->getAllSubscribers();
//
//    expect($result)->toBeInstanceOf(Collection::class);
//    expect($result)->toHaveCount(3);
//    expect($result->pluck('email')->toArray())->toContain('test1@example.com', 'test2@example.com', 'test3@example.com');
// });
//
// it('returns empty collection when no subscribers exist', function () {
//    $result = $this->service->getAllSubscribers();
//
//    expect($result)->toBeInstanceOf(Collection::class);
//    expect($result)->toHaveCount(0);
// });
//
// it('can get subscriber by id', function () {
//    $subscriber = Newsletter::factory()->create(['email' => 'test@example.com']);
//
//    $result = $this->service->getSubscriberById($subscriber->id);
//
//    expect($result)->toBeInstanceOf(Newsletter::class);
//    expect($result->email)->toBe('test@example.com');
//    expect($result->id)->toBe($subscriber->id);
// });
//
// it('returns null when subscriber not found by id', function () {
//    $result = $this->service->getSubscriberById(999);
//
//    expect($result)->toBeNull();
// });
//
// it('can delete subscriber by model instance', function () {
//    $subscriber = Newsletter::factory()->create(['email' => 'test@example.com']);
//
//    $result = $this->service->deleteSubscriberById($subscriber);
//
//    expect($result)->toBe(true);
//    expect(Newsletter::find($subscriber->id))->toBeNull();
// });
//
// it('can delete all subscribers', function () {
//    Newsletter::factory()->create(['email' => 'test1@example.com']);
//    Newsletter::factory()->create(['email' => 'test2@example.com']);
//    Newsletter::factory()->create(['email' => 'test3@example.com']);
//
//    expect(Newsletter::count())->toBe(3);
//
//    $this->service->deleteAllSubscribers();
//
//    expect(Newsletter::count())->toBe(0);
// });
//
// it('handles delete all subscribers when no subscribers exist', function () {
//    expect(Newsletter::count())->toBe(0);
//
//    $this->service->deleteAllSubscribers();
//
//    expect(Newsletter::count())->toBe(0);
// });
//
// it('can store new subscriber', function () {
//    $subscriberData = ['email' => 'test@example.com'];
//
//    $result = $this->service->store($subscriberData);
//
//    expect($result)->toBeInstanceOf(Newsletter::class);
//    expect($result->email)->toBe('test@example.com');
//    expect(Newsletter::count())->toBe(1);
// });
//
// it('can store multiple subscribers', function () {
//    $this->service->store(['email' => 'test1@example.com']);
//    $this->service->store(['email' => 'test2@example.com']);
//    $this->service->store(['email' => 'test3@example.com']);
//
//    expect(Newsletter::count())->toBe(3);
//    expect(Newsletter::pluck('email')->toArray())->toContain('test1@example.com', 'test2@example.com', 'test3@example.com');
// });
//
// it('handles database constraints when storing subscriber', function () {
//    Newsletter::factory()->create(['email' => 'test@example.com']);
//
//    expect(fn () => $this->service->store(['email' => 'test@example.com']))
//        ->toThrow(Exception::class);
// });
