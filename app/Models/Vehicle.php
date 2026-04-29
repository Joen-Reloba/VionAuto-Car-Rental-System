<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_ID';

    protected $fillable = [
        'brand',
        'model',
        'color',
        'plate_no',
        'category',
        'daily_rate',
        'description',
        'status',
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'vehicle_ID', 'vehicle_ID');
    }

    public function images()
    {
        return $this->hasMany(VehicleImg::class, 'vehicle_id', 'vehicle_ID');
    }

    public function primaryImage()
    {
        return $this->hasOne(VehicleImg::class, 'vehicle_id', 'vehicle_ID')->where('is_primary', true);
    }

    // Accessors
    public function getNameAttribute()
    {
        return $this->brand . ' ' . $this->model;
    }
}