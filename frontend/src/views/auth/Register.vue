<template>
  <v-container class="fill-height">
    <v-layout wrap justify-center align-center>
      <v-flex xs12 sm8 md6 lg5>
        <v-card class="register-card elevation-14 px-2 pa-md-6">
          <v-card-text>
            <div class="d-flex justify-center">
              <icon-base width="20" height="20" class="mr-2 icon-check-square">
                <icon-check-square-logo />
              </icon-base>
              <span
                class="text-h5 font-weight-light primary--text text--darken-1"
              >
                Money Management App
              </span>
            </div>

            <form
              @submit.prevent.stop="register"
              ref="form"
              lazy-validation
              class="mt-2"
            >
              <v-text-field
                v-model="name"
                @blur="$v.name.$touch()"
                tabindex="1"
                type="text"
                label="Name"
                autofocus
                hide-details
                :class="nameErrors.length && 'input-errors'"
                :color="nameErrors.length ? 'error' : null"
              >
                <template v-slot:prepend-inner>
                  <icon-base
                    width="16"
                    height="16"
                    class="mr-1"
                    :class="iconNameStyle"
                  >
                    <icon-user />
                  </icon-base>
                </template>
                <template v-slot:append v-if="name">
                  <v-btn small icon tabindex="-1" @click="name = ''">
                    <icon-base width="18" height="18">
                      <icon-close />
                    </icon-base>
                  </v-btn>
                </template>
              </v-text-field>
              <div class="v-text-field__details mt-1 ml-6">
                <div class="v-messages theme--light error--text" role="alert">
                  <div class="v-messages__wrapper">
                    <div class="v-messages__message">{{ nameErrors[0] }}</div>
                  </div>
                </div>
              </div>

              <v-text-field
                v-model="email"
                @blur="$v.email.$touch()"
                tabindex="2"
                type="email"
                label="Email"
                hide-details
                :class="emailErrors.length && 'input-errors'"
                :color="emailErrors.length ? 'error' : null"
              >
                <template v-slot:prepend-inner>
                  <icon-base
                    width="16"
                    height="16"
                    class="mr-1"
                    :class="iconMailStyle"
                  >
                    <icon-mail />
                  </icon-base>
                </template>
                <template v-slot:append v-if="email">
                  <v-btn small icon tabindex="-1" @click="email = ''">
                    <icon-base width="18" height="18">
                      <icon-close />
                    </icon-base>
                  </v-btn>
                </template>
              </v-text-field>
              <div class="v-text-field__details mt-1 ml-6">
                <div class="v-messages theme--light error--text" role="alert">
                  <div class="v-messages__wrapper">
                    <div class="v-messages__message">{{ emailErrors[0] }}</div>
                  </div>
                </div>
              </div>

              <v-text-field
                v-model="password"
                @blur="$v.password.$touch()"
                class="mt-2"
                tabindex="3"
                :type="passwordType"
                label="Password"
                hide-details
                :class="passwordErrors.length && 'input-errors'"
                :color="passwordErrors.length ? 'error' : null"
              >
                <template v-slot:prepend-inner>
                  <icon-base
                    width="16"
                    height="16"
                    class="mr-1"
                    :class="iconLockStyle"
                  >
                    <icon-lock />
                  </icon-base>
                </template>
                <template v-slot:append>
                  <v-btn
                    v-if="password"
                    tabindex="-1"
                    small
                    icon
                    class="mr-n1"
                    @click="typePassword = !typePassword"
                  >
                    <icon-base width="18" height="18" class="text-subtitle">
                      <icon-eye />
                    </icon-base>
                  </v-btn>
                  <v-btn
                    v-if="password"
                    small
                    icon
                    class="mr-n1"
                    @click="password = ''"
                    tabindex="-1"
                  >
                    <icon-base width="18" height="18" class="text-subtitle">
                      <icon-close />
                    </icon-base>
                  </v-btn>
                </template>
              </v-text-field>
              <div class="v-text-field__details mt-1 ml-6">
                <div class="v-messages theme--light error--text" role="alert">
                  <div class="v-messages__wrapper">
                    <div class="v-messages__message">
                      {{ passwordErrors[0] }}
                    </div>
                  </div>
                </div>
              </div>

              <v-text-field
                v-model="confirmPassword"
                @blur="$v.confirmPassword.$touch()"
                class="mt-2"
                tabindex="4"
                :type="confirmPasswordType"
                label="Confirm Password"
                hide-details
                :class="confirmPasswordErrors.length && 'input-errors'"
                :color="confirmPasswordErrors.length ? 'error' : null"
              >
                <template v-slot:prepend-inner>
                  <icon-base
                    width="16"
                    height="16"
                    class="mr-1"
                    :class="iconLockStyle"
                  >
                    <icon-lock />
                  </icon-base>
                </template>
                <template v-slot:append>
                  <v-btn
                    v-if="confirmPassword"
                    tabindex="-1"
                    small
                    icon
                    class="mr-n1"
                    @click="typeConfirmPassword = !typeConfirmPassword"
                  >
                    <icon-base width="18" height="18" class="text-subtitle">
                      <icon-eye />
                    </icon-base>
                  </v-btn>
                  <v-btn
                    v-if="confirmPassword"
                    small
                    icon
                    class="mr-n1"
                    @click="confirmPassword = ''"
                    tabindex="-1"
                  >
                    <icon-base width="18" height="18" class="text-subtitle">
                      <icon-close />
                    </icon-base>
                  </v-btn>
                </template>
              </v-text-field>
              <div class="v-text-field__details mt-1 ml-6">
                <div class="v-messages theme--light error--text" role="alert">
                  <div class="v-messages__wrapper">
                    <div class="v-messages__message">
                      {{ confirmPasswordErrors[0] }}
                    </div>
                  </div>
                </div>
              </div>

              <v-btn
                block
                :loading="loading"
                :disabled="loading"
                type="submit"
                depressed
                class="mt-6 primary"
                >Register</v-btn
              >
            </form>

            <div class="mt-8 px-1">
              <span class="blue-grey--text text--darken-3"
                >Already have an account?</span
              >
              <router-link
                @mousedown.native.prevent
                :to="{ name: 'auth.login' }"
                class="ml-1"
                >Login</router-link
              >
            </div>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import { validationMixin } from "vuelidate";
import { email, minLength, required, sameAs } from "vuelidate/lib/validators";
import messagesMixin from "@/mixins/messagesMixin";

export default {
  name: "Register",

  mixins: [messagesMixin, validationMixin],

  data() {
    return {
      confirmPassword: "",
      name: "",
      email: "",
      loader: null,
      loading: false,
      password: "",
      typeConfirmPassword: true,
      typePassword: true,
    };
  },

  computed: {
    confirmPasswordErrors() {
      const errors = [];
      if (!this.$v.confirmPassword.$dirty) return errors;
      !this.$v.confirmPassword.required &&
        errors.push("Confirm Password is required");
      !this.$v.confirmPassword.sameAsPassword &&
        errors.push("Both passwords must match");
      return errors;
    },

    confirmPasswordType() {
      return this.typeConfirmPassword ? "password" : "text";
    },

    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      !this.$v.email.email && errors.push("Must be valid email");
      !this.$v.email.required && errors.push("Email is required");
      return errors;
    },

    iconLockStyle() {
      return this.password && !this.passwordErrors.length
        ? "primary--text"
        : this.passwordErrors.length
        ? "error--text text--lighten-1"
        : "grey--text text--darken-1";
    },

    iconMailStyle() {
      return this.email && !this.emailErrors.length
        ? "primary--text"
        : this.emailErrors.length
        ? "error--text text--lighten-1"
        : "grey--text text--darken-1";
    },

    iconNameStyle() {
      return this.name && !this.nameErrors.length
        ? "primary--text"
        : this.nameErrors.length
        ? "error--text text--lighten-1"
        : "grey--text text--darken-1";
    },

    nameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.minLength &&
        errors.push("Name must be at least 2 characters long");
      !this.$v.name.required && errors.push("Name is required");
      return errors;
    },

    passwordErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      !this.$v.password.minLength &&
        errors.push("Password must be at least 6 characters long");
      !this.$v.password.required && errors.push("Password is required");
      return errors;
    },

    passwordType() {
      return this.typePassword ? "password" : "text";
    },

    userAvatar() {
      return localStorage.getItem("userAvatar");
    },
  },

  watch: {
    loader() {
      const l = this.loader;
      this[l] = !this[l];
      this.loader = null;
    },
  },

  validations: {
    confirmPassword: { required, sameAsPassword: sameAs("password") },
    email: { required, email },
    name: { required, minLength: minLength(2) },
    password: { required, minLength: minLength(6) },
  },

  methods: {
    register() {
      this.$v.$touch();

      if (this.$v.$invalid) return;

      this.loader = "loading";

      this.$store
        .dispatch("auth/register", {
          email: this.email,
          name: this.name,
          password: this.password,
          password_confirmation: this.confirmPassword,
        })
        .then((data) => {
          this.$router.push({ name: "main" });
        })
        .catch((error) => {
          if (error.response.status >= 400 && error.response.status <= 499) {
            this.showCustomErrorMessage(
              Object.values(error.response.data.errors).flat().join(" "),
              4000
            );
          } else {
            this.showErrorMessage();
          }

          console.log(error);
        })
        .finally(() => {
          this.loader = "loading";
        });
    },
  },
};
</script>

<style scoped>
.register-card >>> .v-input__prepend-inner {
  padding-top: 4px;
}

.register-card .input-errors >>> .v-input__control > .v-input__slot:before {
  border-color: #f56c6c !important;
}

.icon-check-square {
  margin-top: 5px;
}
</style>
