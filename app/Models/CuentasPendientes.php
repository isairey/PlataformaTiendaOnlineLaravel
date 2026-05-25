<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentasPendientes extends Model
{
    use HasFactory;
    protected $table='cuentas_pendientes';
    protected $primaryKey='id';
    public $timestamps = false;
}
