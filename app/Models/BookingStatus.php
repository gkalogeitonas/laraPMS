<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class BookingStatus extends Model
{
    use HasFactory, BelongsToTenant ;

    protected $fillable = ['tenant_id', 'name', 'color'];
}
