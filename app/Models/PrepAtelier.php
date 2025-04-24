<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PrepAtelier extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'notes',
        'commande_id',
        'employe_id',
        'created_at',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //une preparation d'atelier peut etre attendu par une seule commande
    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id', 'id');
    }

    //une preparation d'atelier peut lister plusieurs etapes
    public function etapes()
    {
        return $this->hasMany(Etape::class, 'preparation_id', 'id');
    }

    //une preparation d'atelier peut etre preparee par un seul employe
    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id', 'id');
    }
}
