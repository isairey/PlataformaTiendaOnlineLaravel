<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    use HasFactory;
    protected $table='historial_de _transacciones _y _movimientos';
    protected $primaryKey='id';
    public $timestamps = false;
}
