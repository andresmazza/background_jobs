<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
///background-jobs/run

it('dashshboard post  returns a successful response', function () {
    $response = $this->post('/background-jobs/run');

    $response->assertStatus(302);
});