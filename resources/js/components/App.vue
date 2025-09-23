<template>
  <div>
    <h1>Bienvenido a AssistaChain</h1>
    <router-link to="/dashboard">Ir al Dashboard</router-link>
    <router-link to="/registrar-asistencia">Registrar Asistencia</router-link>

    <router-view></router-view> <!-- Este es el lugar donde se renderizan las rutas -->
  </div>
</template>

<script>
import RegisterAttendance from './RegisterAttendance.vue';

export default {
  components: {
    RegisterAttendance,
  },
};
</script>
