<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TenantScope;
class BookingSource extends Model
{
    use HasFactory;
    protected $fillable = ['tenant_id', 'name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function($model) {
            if(session()->has('active_tenant_id')) {
                $model->tenant_id = session()->get('active_tenant_id');
            }
        });
    }
}
