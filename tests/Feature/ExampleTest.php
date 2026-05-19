<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page->component('MarketplaceSales'));
});

test('marketplace redirects to home page', function () {
    $response = $this->get(route('marketplace'));

    $response->assertRedirect(route('home'));
});
