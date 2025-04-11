<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Etape extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'intitule',
    ];

    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    // Une étape peut appartenir à une préparation
    public function preparation()
    {
        return $this->belongsTo(PrepAtelier::class, 'preparation_id', 'id');
    }
}
