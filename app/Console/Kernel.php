<?php
protected function schedule(Schedule $schedule)
{
   
    $schedule->call(function () {
        app()->make(AsistenciaEmpleadoController::class)->marcarAusentesAutomaticamente();
    })->dailyAt('10:30');
}