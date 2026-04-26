<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_ID';  // changed
    public $incrementing = false;        // added

    protected $fillable = [
        'user_ID',
        'birthday',
        'license_no',
        'license_expiry',
        'address',
        'valid_ID',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_ID', 'user_ID');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_user_id', 'user_ID'); // changed
    }

    public function getNameAttribute()
    {
        return $this->user ? trim($this->user->first_name . ' ' . $this->user->middle_name . ' ' . $this->user->last_name) : '';
    }
}