<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

    protected $fillable = [
        'commande_id',
        'note',
        'description',
        'statut',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}