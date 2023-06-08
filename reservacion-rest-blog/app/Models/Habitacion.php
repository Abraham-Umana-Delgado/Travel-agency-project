<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;
    protected $table = 'habitaciones';
    protected $fillable=[
        'tipo',
        'estado', 
        'precio',
        'caracteristicas',
    ];

    public function reserva(){
        return $this->hasMany('App\Models\Reserva');
    }

}
