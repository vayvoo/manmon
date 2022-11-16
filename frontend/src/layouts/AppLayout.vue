<template>
  <v-app class="app-layout">
    <snack-bar ref="snackbar"></snack-bar>

    <v-app-bar class="indigo lighten-5" fixed flat dense>
      <icon-base width="18" height="18" class="mr-2">
        <icon-check-square-logo />
      </icon-base>
      <span class="text-h6 font-weight-light primary--text text--darken-1"
        >QUIZ App</span
      >
      <v-spacer></v-spacer>
      <div>
        <router-link class="mr-4" to="/admin/index">Admin Panel</router-link>
        <router-link to="/quizzes/index">Test Oynasi</router-link>
        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-bind="attrs"
              v-on="on"
              class="text-capitalize font-weight-regular ma-2"
              text
            >
              <icon-base
                width="22"
                height="22"
                class="blue-grey--text text--darken-3 mr-1"
              >
                <icon-user />
              </icon-base>
              <span class="mx-1 blue-grey--text text--darken-3">
                {{ user.name }}
              </span>
              <icon-base
                v-if="attrs['aria-expanded'] === 'true'"
                width="16"
                height="16"
                class="ml-1 blue-grey--text text--darken-2"
              >
                <icon-chevron-up />
              </icon-base>
              <icon-base
                v-else
                width="16"
                height="16"
                class="ml-1 blue-grey--text text--darken-2"
              >
                <icon-chevron-down />
              </icon-base>
            </v-btn>
          </template>

          <v-list dense>
            <v-list-item @click="logout">
              <v-list-item-content>
                <v-list-item-title>
                  <div class="d-flex">
                    <icon-base width="16" height="16">
                      <icon-logout />
                    </icon-base>
                    <span
                      class="mx-2 blue-grey--text text--darken-2 font-weight-regular"
                      >Logout</span
                    >
                  </div>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-app-bar>

    <v-main class="mt-12">
      <transition :name="transitionName" mode="out-in">
        <router-view></router-view>
      </transition>
    </v-main>

    <v-footer padless>
      <v-card flat tile width="100%" class="indigo lighten-5 text-center">
        <v-card-text class="blue-grey--text">
          <v-row align="center" justify="center">
            <span>&copy;</span>
            <span class="mx-1">Created by</span>
            <a href="https://getinfo.uz" target="_blank" class="blue-grey--text"
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
import { mapGetters } from "vuex";

export default {
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

  computed: mapGetters({
    user: "auth/user",
  }),

  methods: {
    logout() {
      this.$store.dispatch("auth/logout");
    },
  },
};
</script>

<style scoped></style>
