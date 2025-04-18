<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Produit extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'nom',
        'reference',
        'prix_referencement',
        'lien_produit_fournisseur',
        'date_livraison_fournisseur',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un produit peut etre stocke dans plusieurs stocks
    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'produit_stock');
    }

    //un produit peut etre contenu dans plusieurs commandes
    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'commande_produit')->withPivot('quantite', 'quantite_stock', 'quantite_client');
    }

    //un produit peut etre fourni par un fournisseur
    public function fournisseurs()
    {
        return $this->belongsToMany(Fournisseur::class, 'fournisseur_produit');
    }
}
