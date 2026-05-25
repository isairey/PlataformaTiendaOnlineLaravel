<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vendidos extends Model
{
    use HasFactory;
    protected $table='vendidos';
    protected $primaryKey='id_productos';
    public $timestamps = false;
}
