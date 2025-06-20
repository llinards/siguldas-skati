<?php

it('returns a successful response opening page in LAT', function () {
    $response = $this->get('/lv');

    $response->assertStatus(200);
});

it('returns a successful response opening page in ENG', function () {
    $this->refreshApplicationWithLocale('en');
    $response = $this->get('/en');

    $response->assertStatus(200);
});
