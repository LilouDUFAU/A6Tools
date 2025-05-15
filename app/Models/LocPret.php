<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocPret extends Model
{
    use hasFactory;

        protected $fillable = [
        'date_debut',
        'date_retour',
        'client_id',
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    // une location / pret peut concerner plusieurs clients
    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    // une location / pret peut concerner plusieurs pcrenouvs
    public function pcrenouvs()
    {
        return $this->belongsToMany(PcRenouv::class, 'loc_pret_p_c_renouv');
    }

}
