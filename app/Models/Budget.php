<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
        'start_date',
        'end_date',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
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
