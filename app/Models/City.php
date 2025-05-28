<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Province;

class City extends Model
{
    protected $fillable = ['name','province_id'];

    protected $hidden = ['created_at', 'updated_at'];

    public function province(){
        return $this->belongsTo(Province::class);
    }
}
