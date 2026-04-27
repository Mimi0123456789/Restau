<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plat extends Model
{
    use HasFactory;

    protected $table = 'plats';
    protected $fillable = [
        'titre_plat',
        'photo',
    ];

    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'menu_plat',
            'plat_id',
            'menu_id'
        );
    }

    public function allergenes()
    {
        return $this->belongsToMany(
            Allergene::class,
            'allergene_plat',
            'plat_id',
            'allergene_id'
        );
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
