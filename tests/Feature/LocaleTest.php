<?php

// test('default locale redirects with prefix', function () {
//    $response = $this->get('/');
//    $response->assertRedirect('/lv');
//    expect(app()->getLocale())->toBe('lv');
// });

test('english locale uses en prefix', function () {
    $this->refreshApplicationWithLocale('en');
    $response = $this->get('/en');
    $response->assertOk();
    expect(app()->getLocale())->toBe('en');
});
