<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'cover_image_path',
        'city_name',
        'city_manger_id',
        
    ];

    public function cityManger()
    {
        return $this->belongsTo(CityManger::class);
    }
}