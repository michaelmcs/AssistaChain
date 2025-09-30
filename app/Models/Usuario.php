<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'usuarios';
    public $timestamps = false;
    protected $rememberTokenName = false;
    protected $fillable = [
        'nombre_usuario', 'contrasena', 'tipo_usuario', 'estado', 'id_empleado'
    ];
    protected $hidden = ['contrasena'];
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
    public function getAuthIdentifierName()
    {
        return 'nombre_usuario';
    }
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }
    public function getPasswordAttribute()
    {
        return $this->contrasena;
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }
}