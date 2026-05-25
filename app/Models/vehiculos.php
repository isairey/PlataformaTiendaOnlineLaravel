<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vehiculos extends Model
{
    use HasFactory;
    protected $table='vehiculos';
    protected $primaryKey='idvehiculos';
    public $timestamps = false;
}
