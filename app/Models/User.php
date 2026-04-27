<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'prenom',
        'nom',
        'telephone',
        'ville',
        'code_postal',
        'pays',
        'adresse_postale',
        'role_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
 
     protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
