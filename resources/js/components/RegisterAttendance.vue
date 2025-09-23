<template>
  <div>
    <h3>Registrar Asistencia</h3>
    <div>
      <label for="rfid">RFID:</label>
      <input v-model="rfid" type="text" id="rfid" placeholder="Ingrese el RFID">
    </div>
    <button @click="registerAttendance">Registrar Asistencia</button>
    <p v-if="message" :class="messageClass">{{ message }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      rfid: '',
      message: '',
      messageClass: '',
    };
  },
  methods: {
    async registerAttendance() {
      const response = await fetch('/registrar-asistencia', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
        body: JSON.stringify({ rfid: this.rfid }),
      });

      const result = await response.json();

      if (response.ok) {
        this.message = 'Asistencia registrada correctamente';
        this.messageClass = 'success';
      } else {
        this.message = result.message || 'Error al registrar asistencia';
        this.messageClass = 'error';
      }
    },
  },
};
</script>

<style scoped>
.success {
  color: green;
}

.error {
  color: red;
}
</style>
