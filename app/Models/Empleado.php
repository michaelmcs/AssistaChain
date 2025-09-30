<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Empleado extends Model
{
    protected $table = 'empleados';
    public $timestamps = false;

    protected $fillable = ['nombre', 'id_rfid'];

    public function asistencia()
    {
        return $this->hasMany(AsistenciaEmpleado::class, 'id_empleado');
    }
}
