<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresasT extends Model
{
    use HasFactory;
    protected $table='empresas';
    protected $primaryKey='id';
    public $timestamps = false;
}
