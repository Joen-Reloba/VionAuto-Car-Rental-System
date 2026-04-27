<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';       // changed (was 'payment')
    protected $primaryKey = 'payment_ID';

    protected $fillable = [
        'booking_ID',
        'verified_by',
        'payment_type',
        'reference_number',
        'receipt_image',
        'amount_paid',
        'status',
        'payment_date',
        'verified_at',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'verified_at'  => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_ID', 'booking_ID');
    }

    public function rental()
    {
        return $this->booking();
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Staff::class, 'verified_by', 'staff_ID');
    }

    // Accessors — all kept as-is
    public function getCustomerAttribute()
    {
        return $this->booking?->customer;
    }

    public function getPaidAtAttribute()
    {
        return $this->payment_date;
    }

    public function getAmountAttribute()
    {
        return $this->amount_paid;
    }

    public function getPaymentMethodAttribute()
    {
        return $this->payment_type === 'downpayment' ? 'Down Payment' : 'Final Payment';
    }
}