<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_usuario')->unique();
            $table->string('contrasena');
            $table->string('remember_token', 100)->nullable();
            $table->enum('tipo_usuario', ['empleado', 'administrador']);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('id_empleado')->nullable()->constrained('empleados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};