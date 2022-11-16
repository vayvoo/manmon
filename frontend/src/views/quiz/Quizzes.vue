<template>
  <div>
    <h1
      class="text-center font-weight-light blue-grey--text text--darken-3 my-4"
    >
      Select Subjects
    </h1>

    <v-layout wrap justify-center align-center>
      <v-flex xs12 sm8 md6 class="px-4">
        <v-card class="mt-2 mb-10">
          <v-card-title>
            <!-- <router-link :to="'attempt/' + quiz.id">
              {{ quiz.title }}
            </router-link> -->
            <v-row>
              <v-col cols="12">
                <v-autocomplete
                  v-model="subject.first"
                  :items="quizzes"
                  item-value="id"
                  item-text="title"
                  dense
                  label="Birinchi fan"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  v-model="subject.second"
                  :items="quizzes"
                  item-value="id"
                  item-text="title"
                  dense
                  label="Ikkinchi fan"
                ></v-autocomplete>
              </v-col>
              <v-col>
                <v-row>
                  <v-spacer></v-spacer>
                  <v-btn depressed color="primary" @click="startQuiz()"
                    >Testni boshlash</v-btn
                  >
                  <v-spacer></v-spacer>
                </v-row>
              </v-col>
            </v-row> </v-card-title
        ></v-card>
      </v-flex>
    </v-layout>
  </div>
</template>

<script>
import { validationMixin } from "vuelidate";
import { maxLength, required } from "vuelidate/lib/validators";
import messagesMixin from "@/mixins/messagesMixin";

export default {
  name: "Todos",

  mixins: [messagesMixin, validationMixin],

  data() {
    return {
      loaded: false,
      quizzes: [],
      subject: {
        first: null,
        second: null,
      },
    };
  },

  mounted() {
    this.getQuizzes();
  },

  computed: {},

  validations: {
    newTodo: { required, maxLength: maxLength(191) },
  },

  methods: {
    getQuizzes() {
      return this.$http
        .get("/quizzes")
        .then(({ data }) => {
          this.quizzes = data;
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },
    startQuiz() {
      if (this.subject.first != this.subject.second) {
        // console.log(this.subject);
        this.$http
          .post("/startQuiz", this.subject)
          .then(({ data }) => {
            console.log(data);
            // this.newTodo = "";
            // this.$v.$reset();
            this.showSuccessMessage(
              "Hozir keyingi pagega suras bratan faqat shashilmen",
              3000
            );
            this.$router.push("/quizzes/attempt");
          })
          .catch((error) => {
            this.showCustomErrorMessage("Siz testni topshirib bo'lgansiz!");
          });
      } else {
        this.showCustomErrorMessage(
          "Ikkita bir xil fan tanlash mumkin emas!",
          3000
        );
      }
    },
  },
};
</script>

<style lang="css" scoped>
.input-errors >>> .v-input__control > .v-input__slot:before {
  border-color: #f56c6c !important;
}
</style>
