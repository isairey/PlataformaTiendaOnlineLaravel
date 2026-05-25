<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosModel extends Model
{
    use HasFactory;
    protected $table='productos';
    protected $primaryKey='id_productos';
    public $timestamps = false;
    protected $fillable = [
        'codigo',
        'categoria',
        'marca',
        'talla',
        'referencia',
        'color',
        'precio',
    ];
}
