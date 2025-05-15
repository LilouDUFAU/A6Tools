<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Client extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'nom',
        'code_client',
        'numero_telephone',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un client peut commander plusieurs commandes
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'client_id', 'id');
    }
    
    //un client êut etre concerne par plusieurs pannes
    public function pannes()
    {
        return $this->belongsToMany(Panne::class, 'client_panne ');
    }

    // un client peut avoir plusieurs locations / prets de pcrenouv
    public function locPrets()
    {
        return $this->hasMany(LocPret ::class, 'client_id', 'id');
    }
}
