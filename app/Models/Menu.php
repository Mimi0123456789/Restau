<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $fillable = [
        'titre',
        'nombre_personne_minimum',
        'prix_par_personne',
        'regime_id',
        'description',
        'quantite_restante',
        'theme_id'
    ];

    public function plats()
    {
        return $this->belongsToMany(
            Plat::class,
            'menu_plat',
            'menu_id',
            'plat_id'
        );
    }

    public function regime()
    {
        return $this->belongsTo(Regime::class);
    }

    public function theme(){
        return $this->belongsTo(Theme::class);
    }

    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'commande_menu')
            ->withPivot(['quantite', 'prix_unitaire', 'prix_total'])
            ->withTimestamps();
    }
}
