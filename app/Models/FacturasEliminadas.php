<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturasEliminadas extends Model
{
    use HasFactory;
    protected $table='facturaseliminadas';
    protected $primaryKey='id';
    public $timestamps = false;
}
