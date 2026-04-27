<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeStatut extends Model
{
    protected $fillable = [
        'commande_id',
        'statut',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}