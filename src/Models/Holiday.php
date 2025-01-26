<?php

namespace InovantiBank\Holidays\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;

    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'date',
        'type',
        'scope',
        'optional',
        'state',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'id',
        'deleted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'optional' => 'boolean',
    ];
}
