<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_ID';

    protected $fillable = [
        'vehicle_ID',
        'customer_user_id',              // changed
        'approved_by_user_id',           // changed
        'rent_start',
        'rent_end',
        'downpayment',
        'subtotal',
        'tax',
        'total',
        'status',
        'payment_status',
        'returned_at',
        'note',
    ];

    protected $casts = [
        'rent_start'  => 'date',
        'rent_end'    => 'date',
        'returned_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_user_id', 'user_ID'); // changed
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_ID', 'vehicle_ID');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Staff::class, 'approved_by_user_id', 'user_ID'); // changed
    }

    // Accessors — all kept as-is
    public function getRentalDateAttribute()
    {
        return $this->rent_start;
    }

    public function getReturnDateAttribute()
    {
        return $this->rent_end;
    }

    public function getTotalAmountAttribute()
    {
        return $this->total;
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_ID', 'booking_ID');
    }

    public function notifications()
    {
        return $this->hasMany(BookingNotification::class, 'booking_ID', 'booking_ID');
    }
}