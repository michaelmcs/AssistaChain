<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaEmpleado extends Model
{

     use HasFactory;
    protected $table = 'asistencia_empleados';
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'fecha',
        'estado',
        'hash_blockchain',
        'tx_hash',
        'prev_hash',
        'tipo_registro'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}