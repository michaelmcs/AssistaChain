<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registros_actividad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
            $table->string('accion');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();

            $table->index('id_usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registros_actividad');
    }
};