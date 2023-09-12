<?php

use App\Models\Unit;
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

it('creates a new unit', function () {
    $this->actingAs($this->user)
        ->post(route('units.store'), [
            'name' => 'Test Unit',
            'symbol' => 'TU',
        ])
        ->assertRedirect('/units');

    $this->assertDatabaseHas('units', [
        'name' => 'Test Unit',
        'symbol' => 'TU',
    ]);
});

it('updates an existing unit', function () {
    $unit = Unit::factory()->create();

    $this->actingAs($this->user)
        ->put(route('units.update', $unit), [
            'name' => 'Updated Unit',
            'symbol' => 'UU',
        ])
        ->assertRedirect('/units');

    $this->assertDatabaseHas('units', [
        'name' => 'Updated Unit',
        'symbol' => 'UU',
    ]);
});

it('deletes an existing unit', function () {
    $unit = Unit::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('units.destroy', $unit))
        ->assertRedirect('/units');

    $this->assertDatabaseMissing('units', [
        'id' => $unit->id,
    ]);
});
