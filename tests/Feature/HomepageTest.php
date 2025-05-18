<?php

use App\Models\User;
use Illuminate\Support\Facades\Config;

it('returns the marketing page when not logged in and marketing is enabled', function () {
    Config::set('app.marketing', true);
    
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Open-source uptime monitoring');
});

it('redirects to dashboard when marketing is disabled', function () {
    Config::set('app.marketing', false);
    
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});

it('returns a redirect to the dashboard when logged in', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/dashboard');
});
