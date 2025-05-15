<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PCRenouv extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'numero_serie',
        'reference',
        'quantite',
        'caracteristiques',
        'type',
        'statut',
        'locPret_id',
    ];
    
    /////////////////////////
    /// ENUMS définis ici ///
    /////////////////////////
    const TYPES = [
        'portable',
        'fixe',
    ];
    const STATUTS = [
        'en stock',
        'prêté',
        'loué',
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////


    // un pcrenouv peut etre stocke dans un magasin
    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'pcrenouv_stock', 'pcrenouv_id', 'stock_id')->withPivot('quantite');
    }


    public function locPret()
    {
        return $this->belongsTo(LocPret::class, 'loc_pret_id', 'id');
    }
}
