<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'date',
    ];
}
