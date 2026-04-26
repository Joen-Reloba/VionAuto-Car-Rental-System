<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';         // added
    protected $primaryKey = 'user_ID';   // changed
    public $incrementing = false;         // added

    protected $fillable = [
        'user_ID',
        'employee_no',
        'position',
        'hired_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_ID', 'user_ID');
    }

    public function approvedBookings()
    {
        return $this->hasMany(Booking::class, 'approved_by_user_id', 'user_ID'); // changed
    }

    public function verifiedPayments()
    {
        return $this->hasMany(Payment::class, 'verified_by_user_id', 'user_ID'); // changed
    }
}