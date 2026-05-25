<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    use HasFactory;
    protected $table='descuento_usuario';
    protected $primaryKey='iddescuentousuario';
    public $timestamps = false;
}
