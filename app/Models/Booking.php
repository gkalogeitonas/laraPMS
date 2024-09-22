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
        $start_date = new \DateTime($this->start_date);
        $end_date = new \DateTime($this->end_date);
        return $start_date->diff($end_date)->days;
    }

    public function getTotalCostAttribute()
    {
        return $this->total_days * $this->price;
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
