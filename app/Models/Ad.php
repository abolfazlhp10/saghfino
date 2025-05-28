<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ad extends Model
{
    use HasFactory;

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
        'transaction_type',
        'property_type',
        'sell_price',
        'rent_price',
        'mortgage_price',
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

    public function scopeByCity($query, $city)
    {
        return $city ? $query->where('city', $city) : $query;
    }

    public function scopeByRooms($query, $rooms)
    {
        return $rooms ? $query->where('rooms', $rooms) : $query;
    }

    public function scopeByFloorNumbers($query, $floorNumbers)
    {
        return $floorNumbers ? $query->where('number_of_floors', $floorNumbers) : $query;
    }

    public function scopeByArea($query, $area)
    {
        return $area ? $query->where('area', '>=', $area) : $query;
    }

    public function scopeByTitle($query, $title)
    {
        return $title ? $query->where('title', 'LIKE', "%" . $title . "%") : $query;
    }

    public function scopeByTrType($query, $tr_type)
    {
        return $tr_type ? $query->where('transaction_type', $tr_type) : $query;
    }

    public function scopeByPrType($query, $pr_type)
    {
        return $pr_type ? $query->where('property_type', $pr_type) : $query;
    }

    public function scopeTypeOfWc($query, $typeOfWc)
    {
        return $typeOfWc ? $query->where('type_of_wc', $typeOfWc) : $query;
    }

    public function scopeHasParking($query, $parking)
    {
        return $parking ? $query->where('parking', $parking) : $query;
    }

    public function scopeHasElevator($query, $hasElevator)
    {
        return $hasElevator ? $query->where('elevator', $hasElevator) : $query;
    }

    public function scopeBySellPriceOrder($query, $order_by)
    {
        return $order_by ? $query->orderBy('sell_price', $order_by) : $query;
    }

    public function scopeByRentPriceOrder($query, $order_by)
    {
        return $order_by ? $query->orderBy('rent_price', $order_by) : $query;
    }

    // Scope for min price filter
    public function scopeMinPrice($query, $tr_type, $minPrice)
    {
        return $minPrice ? $query->where($tr_type . '' . "_price", '>=', $minPrice) : $query;
    }

    // Scope for max price filter
    public function scopeMaxPrice($query, $tr_type, $maxPrice)
    {
        return $maxPrice ? $query->where($tr_type . '' . "_price", '<=', $maxPrice) : $query;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->locale('fa')->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->locale('fa')->diffForHumans();
    }

}
