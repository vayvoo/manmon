import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import axios from 'axios'
import vuetify from './plugins/vuetify'
import upperFirst from 'lodash/upperFirst'
import camelCase from 'lodash/camelCase'

require('@/assets/css/style.css')

Vue.config.productionTip = false

Vue.prototype.$http = axios
Vue.prototype.$http.defaults.baseURL = process.env.VUE_APP_API_URL
Vue.prototype.$http.defaults.headers.post['Content-Type'] = 'application/json'

// Register Icons globally
const requireComponent = require.context(
  './components/icons',
  false,
  /Icon[A-Z]\w+\.(vue|js)$/
)

requireComponent.keys().forEach(fileName => {
  const componentConfig = requireComponent(fileName)

  const componentName = upperFirst(
    camelCase(
      fileName
        .split('/')
        .pop()
        .replace(/\.\w+$/, '')
    )
  )

  Vue.component(
    componentName,
    componentConfig.default || componentConfig
  )
})

new Vue({
  router,
  store,
  created () {
    const userInfo = localStorage.getItem('user')
    if (userInfo) {
      const userData = JSON.parse(userInfo)
      this.$store.commit('auth/SET_USER_DATA', userData)
    }

    axios.interceptors.response.use(
      response => response,
      error => {
        if (error.response.status === 401) {
          this.$router.push('/login')
          this.$store.commit('auth/CLEAR_USER_DATA')
        }
        return Promise.reject(error)
      }
    )
  },
  vuetify,
  render: h => h(App)
}).$mount('#app')
