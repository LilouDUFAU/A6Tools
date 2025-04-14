<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Service extends Model
{
    use HasFactory;
    
    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'nom',
        'description',
        'created_at',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un service peut avoir plusieurs employes
    public function employes()
    {
        return $this->hasMany(User::class, 'service_id', 'id');
    }


    //////////////////////////////
    ////fonctions utilitaires/////
    //////////////////////////////

    /**
     * Récupère tous les services triés par nom.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getNomService()
    {
        return self::all()->sortBy('nom');
    }
}
