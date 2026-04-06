<?php

declare(strict_types = 1);

use Centrex\TallUi\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

uses(TestCase::class)->in(__DIR__);

// ── Database setup for any test that needs it ─────────────────────────────────
// Applied only to Feature/DataTable and Feature/Http directories which hit the DB.

uses()
    ->beforeEach(function (): void {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->date('joined_at')->nullable();
            $table->timestamps();
        });
    })
    ->afterEach(function (): void {
        Schema::dropIfExists('users');
    })
    ->in('Feature/DataTable', 'Feature/Http');
