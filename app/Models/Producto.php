<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table='productos';
    protected $primaryKey='id_productos';
    public $timestamps = false;
    protected $fillable = [
        'referencia',
        'id_categoria',
        'nombre',
        'precio',
        'precioC',
        'estado',
        'fecha_de_vencimiento',
        'cantidad',
    ];
}
