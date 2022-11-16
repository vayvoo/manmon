import axios from 'axios'

export default {
  namespaced: true,

  state: {
    user: null
  },

  getters: {
    user: state => state.user.user
  },

  mutations: {
    CLEAR_USER_DATA (state) {
      localStorage.removeItem('user')
      location.reload()
    },

    SET_USER_DATA (state, payload) {
      state.user = payload

      localStorage.setItem('user', JSON.stringify(payload))

      axios.defaults.headers.common.Authorization = `Bearer ${payload.token}`
    }
  },

  actions: {
    login ({ commit }, credentials) {
      return axios.post('/login', credentials)
        .then(({ data }) => {
          commit('SET_USER_DATA', data)

          return data.user
        })
        .catch((error) => {
          throw error
        })
    },

    logout ({ commit }) {
      return axios
        .post('/logout')
        .then(({ data }) => {
          commit('CLEAR_USER_DATA')
        })
    },

    register ({ commit }, credentials) {
      return axios
        .post('/register', credentials)
        .then(({ data }) => {
          commit('SET_USER_DATA', data)
        })
        .catch((error) => {
          throw error
        })
    }
  }
}
