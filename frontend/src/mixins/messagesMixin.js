export default {
  methods: {
    showErrorMessage() {
      this.$store.commit("SET_SNACKBAR", {
        active: true,
        message: "Oops! Something went wrong",
        timeout: 2000,
        type: "error",
      });
    },

    showCustomErrorMessage(message, timeout = 2000) {
      this.$store.commit("SET_SNACKBAR", {
        active: true,
        message: message,
        timeout: timeout,
        type: "error",
      });
    },

    showSuccessMessage(message, timeout = 2000) {
      this.$store.commit("SET_SNACKBAR", {
        active: true,
        message: message,
        timeout: timeout,
        type: "success",
      });
    },
  },
};
