<template>
  <div>
    <h1
      class="text-center font-weight-light blue-grey--text text--darken-3 my-4"
    >
      Student Info
    </h1>

    <v-layout wrap justify-center align-center>
      <v-flex xs12 sm8 md6 class="px-4">
        <v-card v-if="todos.length" class="mt-2 mb-10">
          <v-list>
            <transition-group name="fade" mode="out-in">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    <span class="font-weight-regular">{{ todo.name }}</span>
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </transition-group>
          </v-list>
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
      todos: [],
      todoToEditId: null,
      todoNameToEdit: "",
      page: 3,
    };
  },

  mounted() {
    Promise.all([this.addTodo(this.page)]).then(() => {
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
    addTodo() {
      this.$http
        .get(
          "https://student.andmiedu.uz/rest/v1/data/student-list?search=jamol",
          {
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "Authorization": "Bearer uZ29nNvThPXbwE7ov8nC-qRR4_BQRBP9",
            },
          }
        )
        .then(({ data }) => {
          this.todos = data.todo;
          console.log(this.todos);
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
