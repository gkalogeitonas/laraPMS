<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'tenant_id'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class);
    }

    public static function ofActiveTenant()
    {
        $tenantId = auth()->user()->getActiveTenant()->id;
        return self::where('tenant_id', $tenantId);
    }
}
