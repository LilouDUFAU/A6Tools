<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocPret extends Model
{
    protected $fillable = [
        'date_pret',
        'date_retour',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function pcRenouv()
    {
        return $this->hasMany(PCRenouv::class, 'pcRenouv_id', 'id');
    }
}
