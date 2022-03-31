<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'date', 'user_id', 'expense_category_id', 'warehouse_id', 'details',
        'amount', 'Ref', 'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'expense_category_id' => 'integer',
        'warehouse_id' => 'integer',
        'amount' => 'double',
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function expense_category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
