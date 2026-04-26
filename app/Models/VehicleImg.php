<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleImg extends Model
{
    use HasFactory;

    protected $table = 'vehicle_imgs';
    protected $primaryKey = 'vehicle_img_id';

    protected $fillable = [
        'vehicle_id',
        'img_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_ID');
    }
}
