<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_de_usuario extends Model
{
    use HasFactory;
    protected $table='tipo_de_usuario';
    protected $primaryKey='idpersonas';
    public $timestamps = false;

}
