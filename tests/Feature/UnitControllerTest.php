<?php

use App\Models\Unit;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

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

it('validates that the name field is required on store', function () {
    $this->actingAs($this->user)
        ->post(route('units.store'), [
            'name' => '',
            'symbol' => 'TU',
        ])
        ->assertSessionHasErrors('name');
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

it('lists paginated units', function () {
    Unit::factory()->count(45)->create();

    $this->actingAs($this->user)
        ->get(route('units.index'))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Unit/Index')
            ->has(
                'list.data',
                30,
                fn (Assert $page) => $page
                ->hasAll(['id', 'name', 'symbol', 'status'])
                ->etc()
            )
        );
});

it('lists paginated units filtered by a search term', function () {
    $unit = Unit::factory()->create(['name' => 'Unit 1', 'symbol' => 'U1']);

    $this->actingAs($this->user)
        ->get(route('units.index', ['filter[search]' => 'Unit 1']))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Unit/Index')
            ->has(
                'list.data',
                1,
                fn (Assert $page) => $page
                ->hasAll(['id', 'name', 'symbol', 'status'])
                ->etc()
                ->where('id', $unit->id)
            )
        );

    $this->actingAs($this->user)
        ->get(route('units.index', ['filter[search]' => 'U1']))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Unit/Index')
            ->has(
                'list.data',
                1,
                fn (Assert $page) => $page
                ->hasAll(['id', 'name', 'symbol', 'status'])
                ->etc()
                ->where('id', $unit->id)
            )
        );
});
