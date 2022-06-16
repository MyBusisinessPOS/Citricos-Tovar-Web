<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'income_category_id' => 'integer',
        'warehouse_id' => 'integer',
        'amount' => 'double',
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function income_category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
