<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergene extends Model
{
    use HasFactory;

    protected $table = 'allergenes';
    protected $fillable = ['libelle'];

    public function plats()
    {
        return $this->belongsToMany(
            Plat::class,
            'allergene_plat',
            'allergene_id',
            'plat_id'
        );
    }
}
