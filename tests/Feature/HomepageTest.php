<?php

use App\Models\User;

it('returns the marketing page when not logged in', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Open-source uptime monitoring');
});

it('returns a redirect to the dashboard when logged in', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/dashboard');
});
