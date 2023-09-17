<?php

use App\Models\Brand;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('prevents unauthenticated users from accessing any CRUD routes', function () {
    $this->get(route('brands.index'))->assertRedirect(route('login'));
    $this->get(route('brands.create'))->assertRedirect(route('login'));
    $this->post(route('brands.store'))->assertRedirect(route('login'));
    $this->get(route('brands.show', 1))->assertRedirect(route('login'));
    $this->get(route('brands.edit', 1))->assertRedirect(route('login'));
    $this->put(route('brands.update', 1))->assertRedirect(route('login'));
    $this->delete(route('brands.destroy', 1))->assertRedirect(route('login'));
});

it('validates that the name field is required on store', function () {
    $this->actingAs($this->user)
        ->post(route('brands.store'), [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('creates a new brand', function () {
    $this->actingAs($this->user)
        ->post(route('brands.store'), [
            'name' => 'Test Brand',
        ])
        ->assertRedirect('/brands');

    $this->assertDatabaseHas('brands', [
        'name' => 'Test Brand',
    ]);
});

it('updates an existing brand', function () {
    $brand = Brand::factory()->create();

    $this->actingAs($this->user)
        ->put(route('brands.update', $brand), [
            'name' => 'Updated Brand',
        ])
        ->assertRedirect('/brands');

    $this->assertDatabaseHas('brands', [
        'name' => 'Updated Brand',
    ]);
});

it('deletes an existing brand', function () {
    $brand = Brand::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('brands.destroy', $brand))
        ->assertRedirect('/brands');

    $this->assertDatabaseMissing('brands', [
        'id' => $brand->id,
    ]);
});

it('lists paginated brands', function () {
    $brands = Brand::factory()->count(10)->create();

    $this->actingAs($this->user)
        ->get(route('brands.index'))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Brand/Index')
            ->has(
                'list.data',
                10,
                fn (Assert $page) => $page
                ->has('id')
                ->has('name')
                ->has('image')
                ->etc()
            )
        );
});

it('searches for brands', function () {
    $brands = Brand::factory()->count(10)->create();

    $this->actingAs($this->user)
        ->get(route('brands.index', ['filter[search]' => $brands->first()->name]))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Brand/Index')
            ->has(
                'list.data',
                1,
                fn (Assert $page) => $page
                ->has('id')
                ->has('name')
                ->has('image')
                ->etc()
            )
        );
});
