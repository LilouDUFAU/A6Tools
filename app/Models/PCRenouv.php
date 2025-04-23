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
        'reference',
        'quantite',
        'caracteristiques',
        'type',
        'statut',
        'employe_id',
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

    //un pcrenouv peut etre enregistre par un employe
    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id', 'id');
    }

    // un pcrenouv peut etre stocke dans un magasin
    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'pcrenouv_stock', 'pcrenouv_id', 'stock_id')->withPivot('quantite');
    }

    // un pcrenouv peut etre prete a plusieurs clients
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_pcrenouv', 'pcrenouv_id', 'client_id')->withPivot('date_pret', 'date_retour');
    }
}
