<?php

//
// use App\Livewire\Admin\NewsletterList;
// use App\Models\Newsletter;
// use App\Services\ErrorLogService;
// use App\Services\FlashMessageService;
// use App\Services\NewsletterService;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Livewire\Livewire;
//
// uses(RefreshDatabase::class);
//
// it('can be rendered', function () {
//    Livewire::test(NewsletterList::class)
//        ->assertStatus(200);
// });
//
// it('loads all subscribers on mount', function () {
//    Newsletter::factory()->create(['email' => 'test1@example.com']);
//    Newsletter::factory()->create(['email' => 'test2@example.com']);
//    Newsletter::factory()->create(['email' => 'test3@example.com']);
//
//    $component = Livewire::test(NewsletterList::class);
//
//    expect($component->get('subscribers'))->toHaveCount(3);
// });
//
// it('loads empty collection when no subscribers exist', function () {
//    $component = Livewire::test(NewsletterList::class);
//
//    expect($component->get('subscribers'))->toHaveCount(0);
// });
//
// it('can destroy single subscriber successfully', function () {
//    $subscriber = Newsletter::factory()->create(['email' => 'test@example.com']);
//
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('success')
//        ->once()
//        ->with('Pieteikums veiksmīgi dzēsts.');
//
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    Livewire::test(NewsletterList::class)
//        ->call('destroy', $subscriber->id);
//
//    expect(Newsletter::find($subscriber->id))->toBeNull();
// });
//
// it('handles destroy when subscriber not found', function () {
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('error')
//        ->once()
//        ->with('Pieteikums nav atrasts vai nevarēja tikt dzēsts.');
//
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    Livewire::test(NewsletterList::class)
//        ->call('destroy', 999);
// });
//
// it('handles destroy failure gracefully', function () {
//    $subscriber = Newsletter::factory()->create(['email' => 'test@example.com']);
//
//    $mockNewsletterService = Mockery::mock(NewsletterService::class);
//    $mockNewsletterService->shouldReceive('getSubscriberById')
//        ->once()
//        ->with($subscriber->id)
//        ->andReturn($subscriber);
//    $mockNewsletterService->shouldReceive('deleteSubscriberById')
//        ->once()
//        ->with($subscriber)
//        ->andThrow(new Exception('Database error'));
//
//    $mockErrorLogService = Mockery::mock(ErrorLogService::class);
//    $mockErrorLogService->shouldReceive('logError')
//        ->once()
//        ->with('Failed to delete subscriber in the newsletter list', Mockery::type(Exception::class), ['subscriber_id' => $subscriber]);
//
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('error')
//        ->once()
//        ->with('Dzēšot pieteikumu, radās kļūda. Lūdzu, mēģiniet vēlreiz.');
//
//    app()->instance(NewsletterService::class, $mockNewsletterService);
//    app()->instance(ErrorLogService::class, $mockErrorLogService);
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    Livewire::test(NewsletterList::class)
//        ->call('destroy', $subscriber->id);
//
//    expect(Newsletter::find($subscriber->id))->not->toBeNull();
// });
//
// it('can destroy all subscribers successfully', function () {
//    Newsletter::factory()->create(['email' => 'test1@example.com']);
//    Newsletter::factory()->create(['email' => 'test2@example.com']);
//    Newsletter::factory()->create(['email' => 'test3@example.com']);
//
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('success')
//        ->once()
//        ->with('Pieteikumi veiksmīgi dzēsti.');
//
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    Livewire::test(NewsletterList::class)
//        ->call('destroyAll');
//
//    expect(Newsletter::count())->toBe(0);
// });
//
// it('handles destroy all failure gracefully', function () {
//    Newsletter::factory()->create(['email' => 'test1@example.com']);
//    Newsletter::factory()->create(['email' => 'test2@example.com']);
//
//    $mockNewsletterService = Mockery::mock(NewsletterService::class);
//    $mockNewsletterService->shouldReceive('deleteAllSubscribers')
//        ->once()
//        ->andThrow(new Exception('Database error'));
//
//    $mockErrorLogService = Mockery::mock(ErrorLogService::class);
//    $mockErrorLogService->shouldReceive('logError')
//        ->once()
//        ->with('Failed to delete all subscribers in the newsletter list', Mockery::type(Exception::class), []);
//
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('error')
//        ->once()
//        ->with('Dzēšot pieteikumus, radās kļūda. Lūdzu, mēģiniet vēlreiz.');
//
//    app()->instance(NewsletterService::class, $mockNewsletterService);
//    app()->instance(ErrorLogService::class, $mockErrorLogService);
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    Livewire::test(NewsletterList::class)
//        ->call('destroyAll');
//
//    expect(Newsletter::count())->toBe(2);
// });
//
// it('refreshes subscribers list after successful destroy', function () {
//    $subscriber1 = Newsletter::factory()->create(['email' => 'test1@example.com']);
//    $subscriber2 = Newsletter::factory()->create(['email' => 'test2@example.com']);
//    $subscriber3 = Newsletter::factory()->create(['email' => 'test3@example.com']);
//
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('success')
//        ->once()
//        ->with('Pieteikums veiksmīgi dzēsts.');
//
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    $component = Livewire::test(NewsletterList::class);
//
//    expect($component->get('subscribers'))->toHaveCount(3);
//
//    $component->call('destroy', $subscriber2->id);
//
//    expect($component->get('subscribers'))->toHaveCount(2);
//    expect($component->get('subscribers')->pluck('email')->toArray())->toContain('test1@example.com', 'test3@example.com');
//    expect($component->get('subscribers')->pluck('email')->toArray())->not->toContain('test2@example.com');
// });
//
// it('refreshes subscribers list after successful destroy all', function () {
//    Newsletter::factory()->create(['email' => 'test1@example.com']);
//    Newsletter::factory()->create(['email' => 'test2@example.com']);
//    Newsletter::factory()->create(['email' => 'test3@example.com']);
//
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('success')
//        ->once()
//        ->with('Pieteikumi veiksmīgi dzēsti.');
//
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    $component = Livewire::test(NewsletterList::class);
//
//    expect($component->get('subscribers'))->toHaveCount(3);
//
//    $component->call('destroyAll');
//
//    expect($component->get('subscribers'))->toHaveCount(0);
// });
//
// it('handles destroy all when no subscribers exist', function () {
//    $mockFlashMessageService = Mockery::mock(FlashMessageService::class);
//    $mockFlashMessageService->shouldReceive('success')
//        ->once()
//        ->with('Pieteikumi veiksmīgi dzēsti.');
//
//    app()->instance(FlashMessageService::class, $mockFlashMessageService);
//
//    Livewire::test(NewsletterList::class)
//        ->call('destroyAll');
//
//    expect(Newsletter::count())->toBe(0);
// });
//
// it('uses correct layout for rendering', function () {
//    $component = Livewire::test(NewsletterList::class);
//
//    expect($component->payload['serverMemo']['htmlHash'])->not->toBeNull();
// });
//
// it('properly injects dependencies through boot method', function () {
//    $component = Livewire::test(NewsletterList::class);
//
//    // Component should render without errors, indicating proper dependency injection
//    expect($component->get('subscribers'))->not->toBeNull();
// });
