<template>
  <div>
    <h1>Dashboard</h1>
    <p>Bienvenido, {{ user.name }}</p>
    <button @click="logout">Cerrar sesión</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {},
    };
  },
  created() {
    this.fetchUser();
  },
  methods: {
    fetchUser() {
      axios
        .get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          this.user = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    logout() {
      localStorage.removeItem('token');
      this.$router.push('/');
    },
  },
};
</script>
