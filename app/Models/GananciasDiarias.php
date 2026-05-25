<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GananciasDiarias extends Model
{
    use HasFactory;
    protected $table='ganancias_diarias';
    protected $primaryKey='id';
    public $timestamps = false;
}
