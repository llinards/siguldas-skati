<?php

use Livewire\Mechanisms\HandleRequests\EndpointResolver;

it('has a reachable livewire update endpoint', function () {
    $response = $this->postJson(EndpointResolver::updatePath(), [
        'components' => [
            [
                'snapshot' => '{}',
                'updates' => [],
                'calls' => [],
            ],
        ],
    ], ['X-Livewire' => '1']);

    // 500 is expected (invalid snapshot), but 404 means the route is broken
    expect($response->status())->not->toBe(404);
});
