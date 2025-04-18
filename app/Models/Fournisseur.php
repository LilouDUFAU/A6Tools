<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Fournisseur extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'nom'
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un fournisseur peut fournir plusieurs produits
    public function produits()
    {
        return $this->hasMany(Produit::class, 'fournisseur_id', 'id');
    }

    //un fournisseur peut etre concerne par plusieurs pannes
    public function pannes()
    {
        return $this->hasMany(Panne::class, 'fournisseur_id', 'id');
    }
}
