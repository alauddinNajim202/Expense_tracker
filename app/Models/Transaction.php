<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'type',
        'transaction_date',
        'description',
        'income_source',
        'expense_location',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];





    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id','id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }





}
