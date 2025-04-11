<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Permission extends Model
{
    use HasFactory;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    protected $fillable = [
        'nom',
        'description',
        'code',
        'estActive',
        'created_at',
    ];

    
    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //une permission peut etre attribuee a plusieurs roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
