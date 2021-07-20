<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'idrol',
        'usuario',
        'password',
        'email',
        'condicion',
    ];

    public $timestamps = false;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function rol()
    {
        return $this->belongsTo('App\Models\Rol');
    }

    public function persona()
    {
        return $this->belongsTo('App\Models\Persona');
    }



    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
