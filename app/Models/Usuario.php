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

    // Agrega esta propiedad para ignorar remember token
    protected $rememberTokenName = false;

    protected $fillable = [
        'nombre_usuario', 'contrasena', 'tipo_usuario', 'estado', 'id_empleado'
    ];

    protected $hidden = ['contrasena'];
    // Especificar el campo de contraseña para Auth
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Especificar el campo de identificador para Auth
    public function getAuthIdentifierName()
    {
        return 'nombre_usuario';
    }

    // Mutator para hashear la contraseña automáticamente
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }

    // Accessor para el campo password (opcional, para compatibilidad)
    public function getPasswordAttribute()
    {
        return $this->contrasena;
    }

    // Mutator para el campo password (opcional, para compatibilidad)
    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }
}