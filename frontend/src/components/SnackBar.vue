<template>
  <v-snackbar
    v-model="snackbar.active"
    :color="snackbarColor"
    top
    right
    :timeout="snackbar.timeout"
    max-width="400px"
  >
    <div class="d-flex align-center">
      <v-avatar
        size="30"
        class="mr-3 pa-1 white"
      >
        <icon-base
          v-if="snackbar.type === 'success'"
          class="success--text"
        >
          <icon-check/>
        </icon-base>
        <icon-base
          v-else
          class="error--text"
        >
          <icon-alert-circle/>
        </icon-base>
      </v-avatar>
      <span>{{ snackbar.message }}</span>
    </div>
  </v-snackbar>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters([
      'snackbar'
    ]),

    snackbarColor () {
      return this.snackbar.type === 'success' ? 'success' : 'error'
    }
  }
}
</script>

<style scoped>

</style>
