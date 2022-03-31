<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'code', 'adresse', 'phone', 'country', 'email', 'city',
    ];

    protected $casts = [
        'code' => 'integer',
    ];

}
