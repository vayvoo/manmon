import Vue from 'vue'
import Vuetify from 'vuetify/lib/framework'

Vue.use(Vuetify)

export default new Vuetify({
  theme: {
    themes: {
      light: {
        primary: '#0f5f84',
        secondary: '#623827',
        accent: '#9783f3',
        error: '#F56C6C',
        info: '#909399',
        success: '#17c3b2',
        warning: '#E6A23C'
      }
    }
  }
})
