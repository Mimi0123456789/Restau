<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $table = 'commandes';
    protected $fillable = [
        'user_id',
        'date_commande',
        'date_prestation',
        'heure_livraison',
        'prix_menu',
        'nombre_personne',
        'prix_livraison',
        'statut',
        'pret_materiel',
        'restitution_materiel',
    ];

    protected $casts = [
        'pret_materiel' => 'boolean',
        'restitution_materiel' => 'boolean',
        'date_commande' => 'date',
        'date_prestation' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'commande_menu')
            ->withPivot(['quantite', 'prix_unitaire', 'prix_total'])
            ->withTimestamps();
    }
    
    public function avis()
    {
        return $this->hasOne(Avis::class);
    }

    public function statuts()
    {
        return $this->hasMany(CommandeStatut::class)->orderBy('created_at');
    }
}
