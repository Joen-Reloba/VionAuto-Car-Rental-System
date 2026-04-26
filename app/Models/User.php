<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Staff;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_ID';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'role',
        'status',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_ID', 'user_ID');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_ID', 'user_ID');
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Customer::class, 'user_ID', 'customer_user_id', 'user_ID', 'user_ID');
    }

    protected string $username = 'username';

    public function username(): string  // Add this method
    {
        return 'username';
    }

    // Accessors
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }


}