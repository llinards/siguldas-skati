
<?php

use App\Livewire\Admin\NewsletterList;
use App\Models\Newsletter;
use App\Models\User;
use App\Services\ErrorLogService;
use App\Services\FlashMessageService;
use App\Services\NewsletterService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Set up service mocks
    $this->newsletterService = $this->mock(NewsletterService::class);
    $this->flashMessageService = $this->mock(FlashMessageService::class);
    $this->errorLogService = $this->mock(ErrorLogService::class);

    // Bind mocks to container
    $this->app->instance(NewsletterService::class, $this->newsletterService);
    $this->app->instance(FlashMessageService::class, $this->flashMessageService);
    $this->app->instance(ErrorLogService::class, $this->errorLogService);
});

test('component renders successfully and displays all subscribers', function () {
    $subscribers = new Collection([
        Newsletter::factory()->create(),
        Newsletter::factory()->create(),
        Newsletter::factory()->create(),
    ]);

    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn($subscribers);

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(NewsletterList::class)
        ->assertStatus(200)
        ->assertViewHas('subscribers', function ($viewSubscribers) {
            return $viewSubscribers->count() === 3;
        });
});

test('deletes subscriber from database and shows success message', function () {
    $subscriber = Newsletter::factory()->create();

    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn(new Collection([$subscriber]));

    $this->newsletterService->shouldReceive('getSubscriberById')
        ->atLeast()->once()
        ->with($subscriber->id)
        ->andReturn($subscriber);

    $this->newsletterService->shouldReceive('deleteSubscriberById')
        ->atLeast()->once()
        ->with($subscriber)
        ->andReturn(true);

    $this->flashMessageService->shouldReceive('success')
        ->atLeast()->once()
        ->with('Pieteikums veiksmīgi dzēsts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(NewsletterList::class)
        ->call('destroy', $subscriber->id)
        ->assertHasNoErrors();
});

test('handles subscriber not found during deletion with error message', function () {
    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn(new Collection([]));

    $this->newsletterService->shouldReceive('getSubscriberById')
        ->atLeast()->once()
        ->with(999)
        ->andReturn(null);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Pieteikums nav atrasts vai nevarēja tikt dzēsts.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(NewsletterList::class)
        ->call('destroy', 999)
        ->assertHasNoErrors();
});

test('handles error during subscriber deletion', function () {
    $subscriber = Newsletter::factory()->create();
    $exception = new \Exception('Deletion failed');

    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn(new Collection([$subscriber]));

    $this->newsletterService->shouldReceive('getSubscriberById')
        ->atLeast()->once()
        ->with($subscriber->id)
        ->andReturn($subscriber);

    $this->newsletterService->shouldReceive('deleteSubscriberById')
        ->atLeast()->once()
        ->with($subscriber)
        ->andThrow($exception);

    $this->errorLogService->shouldReceive('logError')
        ->atLeast()->once()
        ->with('Failed to delete subscriber in the newsletter list', $exception, ['subscriber_id' => $subscriber]);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Dzēšot pieteikumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.');

    Livewire::test(NewsletterList::class)
        ->call('destroy', $subscriber->id)
        ->assertHasNoErrors();
});

test('deletes all subscribers and shows success message', function () {
    $subscribers = new Collection([
        Newsletter::factory()->create(),
        Newsletter::factory()->create(),
        Newsletter::factory()->create(),
    ]);

    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn($subscribers);

    $this->newsletterService->shouldReceive('deleteAllSubscribers')
        ->atLeast()->once();

    $this->flashMessageService->shouldReceive('success')
        ->atLeast()->once()
        ->with('Pieteikumi veiksmīgi dzēsti.');

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(NewsletterList::class)
        ->call('destroyAll')
        ->assertHasNoErrors();
});

test('handles error during deleting all subscribers', function () {
    $exception = new \Exception('Delete all failed');

    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn(new Collection([Newsletter::factory()->create()]));

    $this->newsletterService->shouldReceive('deleteAllSubscribers')
        ->atLeast()->once()
        ->andThrow($exception);

    $this->errorLogService->shouldReceive('logError')
        ->atLeast()->once()
        ->with('Failed to delete all subscribers in the newsletter list', $exception, []);

    $this->flashMessageService->shouldReceive('error')
        ->atLeast()->once()
        ->with('Dzēšot pieteikumus, radās kļūda. Lūdzu, mēģiniet vēlreiz.');

    Livewire::test(NewsletterList::class)
        ->call('destroyAll')
        ->assertHasNoErrors();
});

test('component handles empty subscribers collection', function () {
    $this->newsletterService->shouldReceive('getAllSubscribers')
        ->atLeast()->once()
        ->andReturn(new Collection([]));

    // Allow error logging (but don't require it)
    $this->errorLogService->shouldReceive('logError')->zeroOrMoreTimes();

    Livewire::test(NewsletterList::class)
        ->assertStatus(200)
        ->assertViewHas('subscribers', function ($subscribers) {
            return $subscribers->isEmpty();
        });
});
