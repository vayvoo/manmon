<template>
  <v-app class="auth-layout">
    <snack-bar ref="snackbar"></snack-bar>

    <div class="fill-height">
      <transition :name="transitionName" mode="out-in">
        <router-view></router-view>
      </transition>
    </div>

    <v-footer padless>
      <v-card flat tile width="100%" class="auth-layout text-center">
        <v-card-text class="white--text">
          <v-row align="center" justify="center">
            <span>&copy;</span>
            <span class="mx-1">Created by</span>
            <a href="https://getinfo.uz" target="_blank" class="white--text"
              >Iqboljon Shorobidinov</a
            >
          </v-row>
        </v-card-text>
      </v-card>
    </v-footer>
  </v-app>
</template>

<script>
import SnackBar from "@/components/SnackBar";

export default {
  name: "AuthLayout",

  components: {
    SnackBar,
  },

  data() {
    return {
      transitionName: "",
    };
  },

  mounted() {
    this.transitionName = "fade";
  },
};
</script>

<style scoped></style>
