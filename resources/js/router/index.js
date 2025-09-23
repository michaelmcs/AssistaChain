import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import RegisterAttendance from '../components/RegisterAttendance.vue';

const routes = [
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
  },
  {
    path: '/registrar-asistencia',
    name: 'register-attendance',
    component: RegisterAttendance,
  },
];

const router = createRouter({
  history: createWebHistory(),  // Utiliza el historial de navegación de Vue para las rutas
  routes,  // Define las rutas que acabamos de establecer
});

export default router;
