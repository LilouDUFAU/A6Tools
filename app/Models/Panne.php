<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Panne extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'etat_client',
        'categorie_materiel',
        'categorie_panne',
        'detail_panne',
        'date_commande',
        'date_panne',
    ];

    ////////////////////
    //enum de la table//
    ////////////////////
    const ETAT_CLIENT = [
        'Ordi de prêt',
        'Échangé',
        'En attente',
    ];

    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //une panne peut concerner un fournisseur
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id', 'id');
    }

    //une panne peut toucher plusieurs clients
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_panne');
    }
}
