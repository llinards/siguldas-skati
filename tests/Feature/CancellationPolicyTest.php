<?php

it('shows the cancellation policy page in Latvian', function () {
    $this->get('/lv/atcelsanas-politika')
        ->assertOk()
        ->assertSee('Atcelšanas politika')
        ->assertSee('1. Bezmaksas atcelšana')
        ->assertSee('ne vēlāk kā 7 dienas pirms ierašanās dienas');
});

it('shows the cancellation policy page in English', function () {
    $this->refreshApplicationWithLocale('en');

    $this->get('/en/atcelsanas-politika')
        ->assertOk()
        ->assertSee('Cancellation policy')
        ->assertSee('1. Free cancellation')
        ->assertSee('up to 7 days before the day of arrival');
});
