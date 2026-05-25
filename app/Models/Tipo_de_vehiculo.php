<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_de_vehiculo extends Model
{
    use HasFactory;
    protected $table='tipo_de_vehiculo';
    protected $primaryKey='id_tipo_vehiculo';
    public $timestamps = false;
}

