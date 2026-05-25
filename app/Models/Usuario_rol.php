<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_rol extends Model
{
    use HasFactory;
    protected $table='usuario_rol';
    protected $primaryKey='	id_usuario_rol';
    public $timestamps = false;
}
