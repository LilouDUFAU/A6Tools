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
        'emplacement',
        'type',
        'statut',
        'created_at',
    ];
    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un pcrenouv peut etre enregistre par un employe
    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id', 'id');
    }
}
