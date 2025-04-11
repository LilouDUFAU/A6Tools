<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Role extends Model
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

    //un role peut avoir plusieurs employes
    public function employes()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    //un role peut avoir plusieurs permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
