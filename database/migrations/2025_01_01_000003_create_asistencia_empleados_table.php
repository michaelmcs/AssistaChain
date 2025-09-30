<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asistencia_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados')->onDelete('cascade');
            $table->timestamp('fecha')->useCurrent();
            $table->enum('estado', ['presente', 'ausente']);
            $table->string('hash_blockchain');
            $table->string('tx_hash')->nullable();
            $table->string('prev_hash')->nullable();
            $table->enum('tipo_registro', ['manual', 'automatico'])->default('manual');
            $table->timestamps();
            $table->index('id_empleado');
            $table->index('tx_hash');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencia_empleados');
    }
};