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
        'type',
        'description',
        'created_at',
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //une action peut etre realisee par un employe
    public function pannes()
    {
        return $this->belongsTo(User::class, 'action_id', 'id');
    }
}
