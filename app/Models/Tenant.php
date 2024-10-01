<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tenant_user');
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingStatuses()
    {
        return $this->hasMany(BookingStatus::class);
    }
}
