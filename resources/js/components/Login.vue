<template>
  <div class="login">
    <h2>Login</h2>
    <form @submit.prevent="login">
      <div>
        <label for="username">Usuario</label>
        <input type="text" v-model="username" required />
      </div>
      <div>
        <label for="password">Contraseña</label>
        <input type="password" v-model="password" required />
      </div>
      <button type="submit">Ingresar</button>
    </form>
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: '',
      error: '',
    };
  },
  methods: {
    login() {
      axios
        .post('/login', {
          username: this.username,
          password: this.password,
        })
        .then((response) => {
          localStorage.setItem('token', response.data.token);
          this.$router.push('/dashboard');  // Redirige al Dashboard
        })
        .catch((error) => {
          this.error = 'Credenciales incorrectas';
        });
    },
  },
};
</script>

<style scoped>
.error {
  color: red;
}
</style>
