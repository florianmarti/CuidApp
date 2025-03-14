<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'address',
        'phone_number',
        'phone_verified',
        'role',
        'is_suspended',
        'is_inactive',
        'confirmation_token',
        'is_confirmed',


    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }
    public function hasVerifiedEmail()
    {
        return $this->is_confirmed;
    }


    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'vigilador_id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
