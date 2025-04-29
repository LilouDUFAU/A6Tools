<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Action extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'intitule',
        'statut',
        'user_id',
        'panne_id',
    ];

    //////////////////////////////
    //// enum de la table ////////
    //////////////////////////////
    const STATUT = [
        'A faire',
        'En cours',
        'Terminé',
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //une action peut etre realisee par un employe
    public function employe()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // une action peut concerner une panne
    public function panne()
    {
        return $this->belongsTo(User::class, 'action_id', 'id');
    }
}
