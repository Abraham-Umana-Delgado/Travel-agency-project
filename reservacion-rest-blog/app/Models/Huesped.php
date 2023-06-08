<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Huesped extends Model
{
    use HasFactory;
    protected $table = 'huesped';
    protected $fillable=[
        'id',
        'nombre',
        'apellidos', 
        'telefono',
        'email',
        'fecha_nacimiento',
        'nacionalidad',
    ];

    public function reserva(){
        return $this->hasMany('App\Models\Reserva');
    }
}
