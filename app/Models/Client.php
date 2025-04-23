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
        return $this->hasMany(Panne::class, 'client_id', 'id');
    }

    // un client peut avoir plusieurs pcrenouv
    public function pcrenouv()
    {
        return $this->belongsToMany(Client::class, 'client_pcrenouv', 'client_id', 'pcrenouv_id')->withPivot('date_pret', 'date_retour');
    }
}
