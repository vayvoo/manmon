<template>
  <div>
    <h1
      class="text-center font-weight-light blue-grey--text text--darken-3 my-4"
    >
      Quizzes List
    </h1>

    <v-layout wrap justify-center align-center>
      <v-flex xs12 sm8 md6 class="px-4">
        <v-card class="mt-2 mb-10">
          <v-card-title
            ><v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search..."
              single-line
              hide-details
              clearable
              outlined
              dense
            ></v-text-field
            ><v-spacer></v-spacer
            ><v-btn depressed color="primary" @click="newQuiz">
              Add Quiz
            </v-btn></v-card-title
          >
          <v-data-table
            :headers="headers"
            :items="quizzes"
            :height="screenHeight"
            class="elevation-1 ma-1"
            :search="search"
            style="border: 1px solid #aaa"
            fixed-header
          >
            <template v-slot:item.id="{ item }">{{
              quizzes.map((v) => v.id).indexOf(item.id) + 1
            }}</template>

            <template v-slot:item.options="{ item }"
              ><v-icon color="primary" @click="editQuiz(item)"
                >mdi-pencil sa</v-icon
              >
              <v-icon color="error" @click="deleteQuiz(item)"
                >mdi-delete</v-icon
              >
            </template>
          </v-data-table>
        </v-card>
      </v-flex>
      <v-dialog
        v-model="QuizAddModal"
        persistent
        max-width="450px"
        @keydown.esc="QuizAddModal = false"
      >
        <v-card>
          <v-card-title>
            <span class="headline">Add Quiz</span>
            <v-spacer></v-spacer>
            <v-btn color="error" x-small fab @click="QuizAddModal = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col class="pt-0" cols="12">
                  <v-text-field
                    v-model="form.title"
                    label="Quiz Title"
                  ></v-text-field>
                </v-col>
                <v-col class="pt-0" cols="12">
                  <v-file-input
                    v-model="files"
                    label="Select File"
                    accept=".xls*"
                  ></v-file-input>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions class="pt-0">
            <v-spacer></v-spacer>
            <v-btn color="green" dark @click="save">Save</v-btn>
            <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-layout>
  </div>
</template>
<script>
import messagesMixin from "@/mixins/messagesMixin";
import axios from "axios";

export default {
  name: "Todos",

  mixins: [messagesMixin],

  data() {
    return {
      loaded: false,
      quizzes: [],
      search: "",
      QuizAddModal: false,
      form: [],
      files: null,
    };
  },

  mounted() {
    // this.getUser(this.$route.params.id);
    this.getQuizzes();
  },

  computed: {
    headers() {
      return [
        { text: "tr", value: "id", sortable: false },
        { text: "Title", value: "title", sortable: false },
        {
          text: "Type",
          value: "type",
          sortable: false,
        },
        {
          text: "Actions",
          align: "center",
          value: "options",
          sortable: false,
          width: 80,
        },
      ];
    },
    screenHeight() {
      return window.innerHeight - 300;
    },
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
    newQuiz() {
      this.QuizAddModal = true;
      this.form = {
        id: Date.now(),
        title: "",
        files: [],
        // source: '',
      };
    },
    editQuiz(item) {
      this.QuizAddModal = true;
      this.form = Object.assign({}, item);
    },
    save() {
      let formData = new FormData();
      formData.append("file", this.files);
      // formData.append('tbn', this.tbn);
      // this.form.files = [];
      // console.log(this.files);
      this.$http.post("quizzes/update", this.form).then((res) => {
        if (this.files) {
          this.$http
            .post("/quizzes/addFile/" + res.data, formData, {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            })
            .then((res2) => {
              this.files = null;
              this.QuizAddModal = false;
              this.getQuizzes();
            });
        } else {
          this.QuizAddModal = false;
          this.getQuizzes();
        }
      });
    },
    // save() {
    //   let formData = new FormData();
    //   formData.append("file", this.files);
    //   console.log(this.files);
    //   this.$http.post("/quizzes/update", this.formData).then(({ data }) => {
    //     console.log(data);
    //     this.getQuizzes();
    //     this.QuizAddModal = false;
    //   });
    // },
    deleteQuiz(item) {
      this.$http
        .delete("/quizzes/delete/" + item.id)
        .then((res) => {
          console.log(res);
          this.getQuizzes();
        })
        .catch((err) => {
          console.log(err);
        });
    },
    submitAnswers() {
      if (this.questions.length == this.form.length) {
        this.$http
          .post("/sendAnswers", this.form)
          .then(({ data }) => {
            this.todos.push(data.todo);
            this.newTodo = "";
            this.$v.$reset();

            this.showSuccessMessage("New Todo was added successfully", 3000);
          })
          .catch((error) => {
            console.log(error);
            this.showErrorMessage();
          });
      } else {
        this.showCustomErrorMessage(
          "Hamma savollarga javob berilmadi. Iltimos, tekshirib qayta urinib ko'ring!",
          3000
        );
        // alert(
        //   "Hamma savollarga javob berilmadi. Iltimos, tekshirib qayta urinib ko'ring!"
        // );
      }
    },
  },
};
</script>
