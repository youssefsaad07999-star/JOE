<?php

use function Pest\Laravel\get;

test('the application returns a successful response for home page ', function () {
    // arrange & act & assert "AAA"
    get(route('home'))
        ->assertOk();
});

test('the application returns a successful response for contact page ', function () {
    // arrange & act & assert "AAA"
    get(route('contact'))
        ->assertOk();
});

test('the application returns a successful response for about page ', function () {
    // arrange & act & assert "AAA"
    get(route('about'))
        ->assertOk();
});

test('the application returns a successful response for login page ', function () {
    // arrange & act & assert "AAA"
    get(route('login'))
        ->assertOk();
});

test('the application returns a successful response for register page ', function () {
    // arrange & act & assert "AAA"
    get(route('register'))
        ->assertOk();
});
