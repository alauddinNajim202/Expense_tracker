<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
        'image',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];


     public function getImageAttribute($value): string | null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        // Check if the request is an API request
        if (request()->is('api/*') && !empty($value)) {
            // Return the full URL for API requests
            return url($value);
        }

        // Return only the path for web requests
        return $value;
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id','id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'category_id','id');

    }




}
