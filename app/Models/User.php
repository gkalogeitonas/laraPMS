<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'tenant_user', 'user_id', 'tenant_id')
                    ->withPivot('tenant_id')
                    ->join('properties as p', 'tenant_user.tenant_id', '=', 'p.tenant_id')
                    ->select('p.*', 'tenant_user.user_id as pivot_user_id', 'tenant_user.tenant_id as pivot_tenant_id');
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user');
    }
}
