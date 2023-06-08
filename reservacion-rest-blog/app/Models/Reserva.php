<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;
    protected $table = 'reserva';
    protected $fillable=[
    'id',
    'id_huesped',
    'fecha_entrada',
    'fecha_salida',
    'precio_f',
    'cant_dias',
    'id_habitacion',
    ];

    public function huesped(){
        return $this->belongsTo('App\Models\Huesped','id_huesped');
    }

    public function habitacion(){
        return $this->belongsTo('App\Models\Habitacion','id_habitacion');
    }
}