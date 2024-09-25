<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['total_days', 'total_cost'];



    public function room()
    {
        return $this->belongsTo(Room::class);
    }


    public function getTotalDaysAttribute()
    {
        $check_in = new \DateTime($this->check_in);
        $check_out = new \DateTime($this->check_out);
        return $check_in->diff($check_out)->days;
    }

    public function getTotalCostAttribute()
    {
        return round($this->total_days * $this->price,2);
    }

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    // public function payment()
    // {
    //     return $this->hasMany(Payment::class);
    // }


}
