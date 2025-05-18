<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstate extends Model
{
    protected $fillable = [
        'agent_id',
        'name',
        'tagline',
        'city',
        'image',
        'rate',
        'address',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
