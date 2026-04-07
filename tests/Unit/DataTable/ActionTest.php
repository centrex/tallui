<?php

declare(strict_types = 1);

use Centrex\TallUi\DataTable\Action;

describe('Action::make()', function (): void {
    it('creates an action with a label', function (): void {
        $action = Action::make('Edit');

        expect($action->label)->toBe('Edit');
    });

    it('defaults color to ghost', function (): void {
        expect(Action::make('Edit')->color)->toBe('ghost');
    });

    it('has no icon by default', function (): void {
        expect(Action::make('Edit')->icon)->toBeNull();
    });
});

describe('Action fluent modifiers', function (): void {
    it('sets icon', function (): void {
        $action = Action::make('Edit')->icon('heroicon-o-pencil');

        expect($action->icon)->toBe('heroicon-o-pencil');
    });

    it('sets color', function (): void {
        $action = Action::make('Delete')->color('error');

        expect($action->color)->toBe('error');
    });

    it('sets route and default key', function (): void {
        $action = Action::make('View')->route('users.show');

        expect($action->route)->toBe('users.show')
            ->and($action->routeKey)->toBe('id');
    });

    it('sets route with custom key', function (): void {
        $action = Action::make('View')->route('users.show', 'uuid');

        expect($action->routeKey)->toBe('uuid');
    });

    it('sets emit event and default key', function (): void {
        $action = Action::make('Delete')->emit('deleteUser');

        expect($action->emitEvent)->toBe('deleteUser')
            ->and($action->emitKey)->toBe('id');
    });

    it('sets emit event with custom key', function (): void {
        $action = Action::make('Delete')->emit('deleteUser', 'uuid');

        expect($action->emitKey)->toBe('uuid');
    });

    it('sets default confirm message', function (): void {
        $action = Action::make('Delete')->confirm();

        expect($action->confirmMessage)->toBe('Are you sure?');
    });

    it('sets custom confirm message', function (): void {
        $action = Action::make('Delete')->confirm('Really delete this?');

        expect($action->confirmMessage)->toBe('Really delete this?');
    });

    it('chains fluently', function (): void {
        $action = Action::make('Delete')
            ->icon('heroicon-o-trash')
            ->color('error')
            ->confirm('Are you sure?')
            ->emit('deleteUser', 'id');

        expect($action->icon)->toBe('heroicon-o-trash')
            ->and($action->color)->toBe('error')
            ->and($action->confirmMessage)->toBe('Are you sure?')
            ->and($action->emitEvent)->toBe('deleteUser');
    });
});

describe('Action::toArray()', function (): void {
    it('serializes all fields', function (): void {
        $arr = Action::make('Edit')
            ->icon('heroicon-o-pencil')
            ->color('info')
            ->route('users.edit', 'id')
            ->toArray();

        expect($arr)
            ->toHaveKey('label', 'Edit')
            ->toHaveKey('icon', 'heroicon-o-pencil')
            ->toHaveKey('color', 'info')
            ->toHaveKey('route', 'users.edit')
            ->toHaveKey('routeKey', 'id')
            ->toHaveKey('emitEvent', null)
            ->toHaveKey('confirmMessage', null);
    });
});
