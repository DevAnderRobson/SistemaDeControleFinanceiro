<?php

it('redirects home to dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});
