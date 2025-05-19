<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealEstateRate extends Model
{
    protected $fillable = ['rate', 'user_id', 'real_estate_id'];

    protected $table='real_estates_rates';
}
