<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /////////////////////////
    //attributs de la table//
    /////////////////////////
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'password',
        'adresse_postale',
        'photo',
        'date_naissance',
        'created_at',
        'service_id',
        'role_id',
    ];

    //////////////////////////////
    //relations entre les tables//
    //////////////////////////////

    //un employe peut passer plusieurs commandes
    public function commandes(){
        return $this->hasMany(Commande::class, 'user_id', 'id');
    }

    //un employe peut preparer plusieurs preparations
    public function preparations(){
        return $this->hasMany(PrepAtelier::class, 'user_id', 'id');
    }

    //un employe travaille dans un service
    public function service(){
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    //un employe peut realiser plusieurs actions
    public function actions(){
        return $this->hasMany(Action::class, 'user_id', 'id');
    }

    //un employe peut enregistrer plusieurs PCRenouv
    public function pcrenouvs(){
        return $this->hasMany(PCRenouv::class, 'user_id', 'id');
    }

    //un employe peut occuper un role
    public function role(){
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    

    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
