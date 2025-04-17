<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Commande extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'intitule',
        'prix_total',
        'etat',
        'urgence',
        'remarque',
        'date_livraison_fournisseur',
        'date_installation_prevue',
        'created_at',
        'client_id',
        'employe_id',
    ];

    /////////////////////////
    /// ENUMS définis ici ///
    /////////////////////////
    const ETATS = [
        'en attente',
        'en cours',
        'terminée',
        'annulée',
    ];

    const URGENCES = [
        'pas urgent',
        'peu urgent',
        'moyennement urgent',
        'urgent',
        'très urgent',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //une commande peut etre commandee par un client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    //une commande peut posseder un bon de livraison
    public function bonLivraison()
    {
        return $this->hasOne(BonLivraison::class, 'bon_livraison_id', 'id');
    }

    //une commande peut attendre une preparation
    public function preparation()
    {
        return $this->hasOne(PrepAtelier::class, 'preparation_id', 'id');
    }

    //une commande peut etre passee par un employe
    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id', 'id');
    }

    //une commande peut contenir plusieurs produits
    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'commande_produit')->withPivot('quantite', 'quantite_stock', 'quantite_client')->withTimestamps();
    }
}
