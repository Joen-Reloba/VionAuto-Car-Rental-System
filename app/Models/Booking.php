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
        'customer_user_id',
        'approved_by_user_id',
        'rent_start',
        'rent_end',
        'downpayment',
        'subtotal',
        'tax',
        'total',
        'extra_charge',
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

    /**
     * Calculate days late based on actual return vs expected return date (rent_end)
     * Returns 0 if returned on time or early
     */
    public function getDaysLateAttribute()
    {
        if (!$this->returned_at) {
            return 0;
        }
        
        try {
            if (!$this->rent_end) {
                return 0;
            }
            
            $expectedDate = \Carbon\Carbon::parse($this->rent_end);
            $actualDate = \Carbon\Carbon::parse($this->returned_at);
            
            // Only count as late if returned AFTER rent_end
            if ($actualDate->gt($expectedDate)) {
                return $actualDate->diffInDays($expectedDate);
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate extra charge: days_late * vehicle daily_rate
     */
    public function calculateExtraCharge()
    {
        $daysLate = $this->days_late;
        $dailyRate = $this->vehicle?->daily_rate ?? 0;
        return max(0, $daysLate * $dailyRate);
    }

    /**
     * Mark as returned and calculate extra charges
     */
    public function markAsReturned()
    {
        $this->returned_at = now();
        $this->extra_charge = $this->calculateExtraCharge();
        $this->total = $this->subtotal + $this->tax + $this->extra_charge;
        return $this;
    }
}