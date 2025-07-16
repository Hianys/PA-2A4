<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'identity_document',
        'driver_license',
        'documents_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'documents_verified' => 'boolean',
            'identity_document' => 'string',
            'driver_license' => 'string',
        ];
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isTrader()
    {
        return $this->role === 'commercant';
    }

    public function isDelivery()
    {
        return $this->role === 'livreur';
    }

    public function isProvider()
    {
        return $this->role === 'prestataire';
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function segmentsTaken()
    {
        return $this->hasMany(TransportSegment::class, 'delivery_id');
    }

    public function wallet()
    {
        return $this->hasOne(\App\Models\Wallet::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $user->wallet()->create();
        });
    }



}
