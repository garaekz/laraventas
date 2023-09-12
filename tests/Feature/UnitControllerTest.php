<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('prevents unauthenticated users from accessing any CRUD routes', function () {
    $this->get('/units')->assertRedirect('/login');
    $this->get('/units/create')->assertRedirect('/login');
    $this->get('/units/1')->assertRedirect('/login');
    $this->get('/units/1/edit')->assertRedirect('/login');
    $this->post('/units', [])->assertRedirect('/login');
    $this->put('/units/1', [])->assertRedirect('/login');
    $this->delete('/units/1', [])->assertRedirect('/login');
});

it('can create a unit', function () {
    $this->actingAs($this->user)
        ->post('/units', [
            'name' => 'Test Unit',
            'symbol' => 'TU',
        ])
        ->assertRedirect('/units');
});
