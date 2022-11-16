<template>
  <div>
    <h1
      class="text-center font-weight-light blue-grey--text text--darken-3 my-4"
    >
      Test oynasi
    </h1>

    <v-layout wrap justify-center align-center>
      <v-flex xs12 sm8 md6 class="px-4">
        <v-card v-if="questions.length" class="mt-2 mb-10">
          <v-list v-for="(q, k) in questions">
            <!-- <div
              v-if="questions.length > 1"
              class="blue-grey--text caption text--darken-1 text-center mt-2"
            >
              Reorder todos in a list using the mouse
            </div> -->
            <!-- {{ form }} -->
            <h2
              class="text-center font-weight-light blue-grey--text text--darken-3 my-4"
            >
              {{ k + 1 }} - fan
            </h2>
            <transition-group name="fade" mode="out-in">
              <v-list-item
                v-for="(question, index) in q"
                :key="index"
                draggable
                @dragstart="pickupTodo($event, $index)"
                @dragenter.prevent
                @dragover.prevent
                @drop.stop="moveTodo($event, $index)"
                class="grabbable"
              >
                <v-list-item-content>
                  <v-list-item-title class="text-wrap">
                    {{ index + 1 }}. {{ question.content }}
                  </v-list-item-title>
                  <v-list-item-title>
                    <v-radio-group class="ml-2">
                      <v-radio
                        v-for="(answer, a) in question.answers"
                        :key="a"
                        :label="`${answer.content}`"
                        :value="answer.id"
                        @change="saveRadio(question.id, answer.id)"
                      ></v-radio>
                    </v-radio-group>
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </transition-group>
          </v-list>
          <v-row>
            <v-spacer></v-spacer>
            <v-btn
              depressed
              class="text-center mb-4"
              @click="submitAnswers"
              color="primary"
            >
              Finish quiz
            </v-btn>
            <v-spacer></v-spacer>
          </v-row>
        </v-card>
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
      newTodo: "",
      questions: [],
      radioGroup: "",
      todoToEditId: null,
      form: [],
      todoNameToEdit: "",
    };
  },

  mounted() {
    Promise.all([this.getQuestions()]).then(() => {
      this.loaded = true;
    });
  },

  computed: {
    newTodoErrors() {
      const errors = [];
      if (!this.$v.newTodo.$dirty) return errors;
      !this.$v.newTodo.required && errors.push("Todo is required");
      return errors;
    },
  },

  validations: {
    newTodo: { required, maxLength: maxLength(191) },
  },

  methods: {
    saveRadio(question, answer) {
      let obj = { question: question, answer: answer };
      if (this.form.length > 0) {
        // for (let i = 0; i < this.form.length; i++) {
        //   if (this.form[i].question == question) {
        //     this.form.splice(i, 1);
        //   } else if (this.form[i].answer != answer) {
        //     this.form.push(obj);
        //   }
        // }
        this.form = this.form.filter((f) => f.question != question);
        this.form.push(obj);
      } else {
        this.form.push(obj);
        // this.showErrorMessage("Vey bittayam javob tanlamadizu))", 3000);
      }
      //   this.form.push(answer);
    },
    submitAnswers() {
      if (
        this.questions[0].length + this.questions[1].length ==
        this.form.length
      ) {
        this.$http
          .post("/sendAnswers", this.form)
          .then(({ data }) => {
            console.log(data);
            // this.newTodo = "";
            // this.$v.$reset();
            this.showSuccessMessage("Your result: " + data, 3000);
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
    addTodo() {
      this.$v.$touch();

      if (this.$v.$invalid) return;

      this.$http
        .post("/todos", {
          name: this.newTodo,
        })
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
    },

    cancelEdit() {
      this.todoToEditId = null;
      this.todoNameToEdit = "";
    },

    deleteTodo(todo) {
      this.$http
        .delete("/todos/" + todo.id)
        .then(({ data }) => {
          this.todos = this.todos.filter((item) => item.id !== todo.id);

          this.showSuccessMessage("Todo has been deleted");
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },

    edit(todo) {
      this.todoToEditId = todo.id;
      this.todoNameToEdit = todo.name;
    },

    getQuestions() {
      console.log(this.$route.params.quizId);
      return this.$http
        .get("/questions")
        .then(({ data }) => {
          this.questions = data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getTodos() {
      return this.$http
        .get("/todos")
        .then(({ data }) => {
          this.todos = data;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    moveTodo(e, toTodoIndex) {
      const toTodo = this.todos[toTodoIndex];

      const fromTodoIndex = e.dataTransfer.getData("from-todo-index");

      const todoToMove = this.todos.splice(fromTodoIndex, 1)[0];

      this.todos.splice(toTodoIndex, 0, todoToMove);

      if (todoToMove.id !== toTodo.id) {
        this.updateSortOrder(todoToMove, toTodo);
      }
    },

    pickupTodo(e, todoIndex) {
      e.dataTransfer.effectAllowed = "move";
      e.dataTransfer.dropEffect = "move";

      e.dataTransfer.setData("from-todo-index", todoIndex);
    },

    updateTodo() {
      this.$http
        .put("/todos/" + this.todoToEditId, {
          name: this.todoNameToEdit,
        })
        .then(({ data }) => {
          const todo = this.todos.find((item) => item.id === this.todoToEditId);
          todo.name = this.todoNameToEdit;
          this.cancelEdit();

          this.showSuccessMessage("Todo has been updated");
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },

    updateSortOrder(todoToMove, toTodo) {
      this.$http
        .put("/update-sort-order", {
          todoToMoveId: todoToMove.id,
          toTodoId: toTodo.id,
        })
        .then(({ data }) => {})
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },

    updateTodoStatus(todo) {
      this.$http
        .put("/todos/update-status/" + todo.id, {
          done: !todo.done,
        })
        .then(({ data }) => {
          todo.done = data.todo.done;
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },
  },
};
</script>

<style lang="css" scoped>
.input-errors >>> .v-input__control > .v-input__slot:before {
  border-color: #f56c6c !important;
}
</style>
