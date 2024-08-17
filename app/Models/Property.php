<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'address', 'description', 'type', 'tenant_id'
    ];


    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
