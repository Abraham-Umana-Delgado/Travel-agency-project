<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Pago extends Model
{
    use HasFactory;
    protected $table = 'pago';
    protected $fillable=[
        'id_reserva',
        'titular_t',
        'n_tarjeta',
        't_tarjeta',
        'cvc',
        'image',
    ];

    public function reserva(){
        return $this->belongsTo('App\Models\Reserva','reserva_id');
    }
}
