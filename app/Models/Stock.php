<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Stock extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'lieux',
        'created_at',
    ];

    /////////////////////////
    /// ENUMS définis ici ///
    /////////////////////////
    const LIEUX = [
        'Mont de Marsan',
        'Aire sur Adour',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un stock peut contenir plusieurs produits
    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'produit_stock');
    }
    
}