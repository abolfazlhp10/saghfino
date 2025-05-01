<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'area',
        'floor',
        'number_of_floors',
        'rooms',
        'city',
        'type_of_wc',
        'elevator',
        'parking',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_ad', 'ad_id', 'user_id');
    }
}
