<template>
  <component v-bind:is="layout"></component>
</template>

<script>
import AppLayout from './layouts/AppLayout'
import AuthLayout from './layouts/AuthLayout'
import DefaultLayout from './layouts/DefaultLayout'
import { mapGetters } from 'vuex'

export default {
  name: 'App',

  components: {
    'app-layout': AppLayout,
    'auth-layout': AuthLayout,
    'default-layout': DefaultLayout
  },

  computed: {
    ...mapGetters([
      'layout'
    ])
  }
}
</script>
