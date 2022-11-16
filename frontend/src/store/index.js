import Vue from "vue";
import Vuex from "vuex";
import auth from "./modules/auth";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    layout: "default-layout",
    backend_url: "http://manmon.loc",
    snackbar: {
      active: false,
      message: "",
      type: "success",
      timeout: 2000,
    },
  },

  getters: {
    layout: (state) => state.layout,
    snackbar: (state) => state.snackbar,
  },

  mutations: {
    SET_LAYOUT(state, payload) {
      state.layout = payload;
    },

    SET_SNACKBAR(state, payload) {
      state.snackbar = payload;
    },
  },

  actions: {},

  modules: {
    auth,
  },
});
