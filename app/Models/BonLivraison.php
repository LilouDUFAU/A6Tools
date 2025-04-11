<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class BonLivraison extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'statut',
        'created_at',
        'date_signature',
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un bon de livraison peut etre possede par une commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id', 'id');
    } 
}
