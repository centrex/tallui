<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    User::create(['name' => 'Alice Smith',  'email' => 'alice@test.com']);
    User::create(['name' => 'Bob Johnson',  'email' => 'bob@test.com']);
    User::create(['name' => 'Charlie Blue', 'email' => 'charlie@test.com']);
});

it('returns 403 for a model not in the allowlist', function (): void {
    config(['tallui.forms.searchable_models' => []]);

    $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => 'ali']))
        ->assertStatus(403);
});

it('returns 422 for a registered name with a non-existent model class', function (): void {
    config(['tallui.forms.searchable_models' => [
        'ghost' => ['model' => 'App\\Models\\GhostModel', 'label' => 'name', 'value' => 'id'],
    ]]);

    $this->getJson(route('tallui.select-search', ['name' => 'ghost', 'q' => 'test']))
        ->assertStatus(422);
});

it('returns matching results for an allowlisted model', function (): void {
    config(['tallui.forms.searchable_models' => [
        'user' => [
            'model'          => User::class,
            'label'          => 'name',
            'value'          => 'id',
            'search_columns' => ['name', 'email'],
        ],
    ]]);

    $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => 'alice']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['label' => 'Alice Smith']);
});

it('returns all results when query is empty', function (): void {
    config(['tallui.forms.searchable_models' => [
        'user' => ['model' => User::class, 'label' => 'name', 'value' => 'id'],
    ]]);

    $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => '']))
        ->assertOk()
        ->assertJsonCount(3);
});

it('returns results with value and label keys', function (): void {
    config(['tallui.forms.searchable_models' => [
        'user' => ['model' => User::class, 'label' => 'name', 'value' => 'id'],
    ]]);

    $response = $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => 'alice']))
        ->assertOk()
        ->json();

    expect($response[0])->toHaveKeys(['value', 'label'])
        ->and($response[0]['label'])->toBe('Alice Smith');
});

it('returns results with an optional sublabel key when configured', function (): void {
    config(['tallui.forms.searchable_models' => [
        'user' => ['model' => User::class, 'label' => 'name', 'sublabel' => 'email', 'value' => 'id'],
    ]]);

    $response = $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => 'alice']))
        ->assertOk()
        ->json();

    expect($response[0])->toHaveKeys(['value', 'label', 'sublabel'])
        ->and($response[0]['sublabel'])->toBe('alice@test.com');
});

it('is case-insensitive for the search query', function (): void {
    config(['tallui.forms.searchable_models' => [
        'user' => ['model' => User::class, 'label' => 'name', 'value' => 'id'],
    ]]);

    $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => 'ALICE']))
        ->assertOk()
        ->assertJsonFragment(['label' => 'Alice Smith']);
});

it('returns matching results for a component-enqueued source', function (): void {
    Cache::put('tallui:select-source:inline-users', [
        'model'          => User::class,
        'label'          => 'name',
        'sublabel'       => 'email',
        'value'          => 'id',
        'search_columns' => ['name', 'email'],
    ], 300);

    $this->getJson(route('tallui.select-search', ['source' => 'inline-users', 'q' => 'alice']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['label' => 'Alice Smith', 'sublabel' => 'alice@test.com']);
});

it('supports searching across configured columns and orders results', function (): void {
    config(['tallui.forms.searchable_models' => [
        'user' => [
            'model'           => User::class,
            'label'           => 'name',
            'value'           => 'id',
            'search_columns'  => ['name', 'email'],
            'order_by'        => 'name',
            'order_direction' => 'asc',
        ],
    ]]);

    $response = $this->getJson(route('tallui.select-search', ['name' => 'user', 'q' => 'test.com']))
        ->assertOk()
        ->json();

    expect($response)->toHaveCount(3)
        ->and($response[0]['label'])->toBe('Alice Smith')
        ->and($response[1]['label'])->toBe('Bob Johnson');
});

it('requires the name parameter', function (): void {
    $this->getJson(route('tallui.select-search', ['q' => 'test']))
        ->assertStatus(422);
});

it('returns 403 when a component-enqueued source is missing', function (): void {
    $this->getJson(route('tallui.select-search', ['source' => 'missing-source', 'q' => 'alice']))
        ->assertStatus(403);
});
