<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'code', 'adresse', 'email', 'phone', 'country', 'city',

    ];

    protected $casts = [
        'code' => 'integer',
    ];
}
