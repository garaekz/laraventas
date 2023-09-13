<?php

use App\Models\Category;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('prevents unauthenticated users from accessing any CRUD routes', function () {
    $this->get(route('categories.index'))->assertRedirect('/login');
    $this->get(route('categories.create'))->assertRedirect('/login');
    $this->get(route('categories.show', 1))->assertRedirect('/login');
    $this->get(route('categories.edit', 1))->assertRedirect('/login');
    $this->post(route('categories.store'), [])->assertRedirect('/login');
    $this->put(route('categories.update', 1), [])->assertRedirect('/login');
    $this->delete(route('categories.destroy', 1), [])->assertRedirect('/login');
});

it('validates that the name field is required on store', function () {
    $this->actingAs($this->user)
        ->post(route('categories.store'), [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('creates a new category', function () {
    $this->actingAs($this->user)
        ->post(route('categories.store'), [
            'name' => 'Test Category',
        ])
        ->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
    ]);
});

it('updates an existing category', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)
        ->put(route('categories.update', $category), [
            'name' => 'Updated Category',
        ])
        ->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', [
        'name' => 'Updated Category',
    ]);
});

it('deletes an existing category', function () {
    $category = Category::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('categories.destroy', $category))
        ->assertRedirect('/categories');

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

it('lists paginated categories', function () {
    Category::factory()->count(45)->create();

    $this->actingAs($this->user)
        ->get(route('categories.index'))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Category/Index')
            ->has(
                'list.data',
                30,
                fn (Assert $page) => $page
                ->hasAll(['id', 'name', 'status'])
                ->etc()
            )
        );
});

it('lists paginated categories filtered by a search term', function () {
    $category = Category::factory()->create(['name' => 'Category 1']);

    $this->actingAs($this->user)
        ->get(route('categories.index', ['filter[search]' => 'Category 1']))
        ->assertInertia(
            fn (Assert $page) => $page
            ->component('Category/Index')
            ->has(
                'list.data',
                1,
                fn (Assert $page) => $page
                ->hasAll(['id', 'name', 'status'])
                ->etc()
                ->where('id', $category->id)
            )
        );

});
