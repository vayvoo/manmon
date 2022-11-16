<template>
  <div>
    <v-layout wrap justify-center align-center>
      <v-flex xs12 sm8 md6 class="px-4">
        <div class="d-flex align-center justify-center">
          <v-spacer></v-spacer>
          <h2
            v-if="showBalanceStatus"
            class="text-center mr-5 font-weight-bold light-blue--text text--darken-4 my-4"
          >
            {{ user.balance }}
          </h2>
          <h2
            v-if="!showBalanceStatus"
            class="text-center mr-5 font-weight-light blue-grey--text text--darken-3 my-4"
          >
            ******
          </h2>
          <v-icon
            v-if="showBalanceStatus"
            large
            color="success"
            @click="showBalanceStatus = !showBalanceStatus"
          >
            mdi-eye
          </v-icon>
          <v-icon
            v-if="!showBalanceStatus"
            large
            color="error"
            @click="showBalanceStatus = !showBalanceStatus"
          >
            mdi-eye-off
          </v-icon>
          <v-spacer></v-spacer>
          <v-icon color="primary" large @click="newAction()"> mdi-plus </v-icon>
        </div>
        <v-text-field
          v-model.trim="$v.newTodo.$model"
          @keydown.enter="addTodo"
          label="New Todo"
          autofocus
          dense
          filled
          hide-details
          :class="newTodoErrors.length && 'input-errors'"
          :color="newTodoErrors.length ? 'error' : null"
        >
          <v-icon v-if="newTodo" slot="append" color="primary" @click="addTodo">
            mdi-plus
          </v-icon>
        </v-text-field>
        <div class="v-text-field__details mt-2 ml-2">
          <div class="v-messages theme--light error--text" role="alert">
            <div class="v-messages__wrapper">
              <div class="v-messages__message">{{ newTodoErrors[0] }}</div>
            </div>
          </div>
        </div>

        <v-progress-linear
          v-if="!loaded"
          color="primary"
          indeterminate
          rounded
          height="4"
        ></v-progress-linear>

        <v-card class="mt-2 mb-10">
          <v-list>
            <div
              class="blue-grey--text caption text--darken-1 text-center mt-2"
            >
              Reorder todos in a list using the mouse
            </div>
            <transition-group name="fade" mode="out-in">
              <v-list-item
                v-for="(todo, $index) in actions"
                :key="todo.id"
                draggable
                @dragstart="pickupTodo($event, $index)"
                @dragenter.prevent
                @dragover.prevent
                @drop.stop="moveTodo($event, $index)"
                class="grabbable"
              >
                <v-list-item-content>
                  <v-list-item-title>
                    <v-text-field
                      v-if="todoToEditId === todo.id"
                      v-model="todoNameToEdit"
                      @keydown.enter="updateTodo"
                      dense
                      class="mt-0 mb-n4"
                    >
                      <template v-slot:append>
                        <v-btn
                          small
                          text
                          tabindex="-1"
                          color="grey"
                          @click="cancelEdit"
                        >
                          Cancel
                        </v-btn>
                        <v-btn
                          v-if="todo.name !== todoNameToEdit"
                          small
                          text
                          tabindex="-1"
                          color="primary"
                          @click="updateTodo"
                        >
                          Update
                        </v-btn>
                      </template>
                    </v-text-field>
                    <span
                      :class="
                        !todo.type
                          ? 'red--text text--accent-2 text-decoration-line-through'
                          : 'blue-grey--text text--darken-3'
                      "
                      >{{ todo.amount }}</span
                    >
                  </v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn icon v-bind="attrs" v-on="on" @click.stop small>
                        <icon-base
                          width="16"
                          height="16"
                          class="my-1 blue-grey--text text--darken-3"
                        >
                          <icon-more-vertical />
                        </icon-base>
                      </v-btn>
                    </template>
                    <v-list dense>
                      <v-list-item @click="edit(todo)">
                        <v-list-item-title>
                          <div class="d-flex">
                            <icon-base
                              width="16"
                              height="16"
                              class="blue-grey--text text--darken-2"
                            >
                              <icon-edit />
                            </icon-base>
                            <span
                              class="mx-2 blue-grey--text text--darken-3 font-weight-regular"
                              >Edit</span
                            >
                          </div>
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteTodo(todo)">
                        <v-list-item-title>
                          <div class="d-flex">
                            <icon-base
                              width="16"
                              height="16"
                              class="error--text"
                            >
                              <icon-trash />
                            </icon-base>
                            <span
                              class="mx-2 error--text text--darken-1 font-weight-regular"
                              >Delete</span
                            >
                          </div>
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </v-list-item-action>
              </v-list-item>
            </transition-group>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
    <v-dialog
      v-model="addBalanceModal"
      persistent
      max-width="450px"
      @keydown.esc="addBalanceModal = false"
    >
      <v-card>
        <v-card-title>
          <span class="headline">Add Balance</span>
          <v-spacer></v-spacer>
          <v-btn color="error" x-small fab @click="addBalanceModal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col class="pt-0" cols="12">
                <v-text-field v-model="balance" label="Amount"></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions class="pt-0">
          <v-spacer></v-spacer>
          <v-btn color="green" dark @click="setBalance()">Save</v-btn>
          <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="addMoneyModal"
      persistent
      max-width="450px"
      @keydown.esc="addMoneyModal = false"
    >
      <v-card>
        <v-card-title>
          <span class="headline">Add Balance</span>
          <v-spacer></v-spacer>
          <v-btn color="error" x-small fab @click="addMoneyModal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col class="pt-0" cols="12">
                <v-select
                  v-model="money.actiontype"
                  :items="actiontypes"
                  label="Type"
                  solo
                ></v-select>
              </v-col>
              <v-col class="pt-0" cols="12">
                <v-text-field
                  v-model="money.amount"
                  label="Amount"
                  type="text"
                  solo
                  dense
                  clearable
                ></v-text-field>
              </v-col>
              <v-col class="pt-0" cols="12">
                <v-text-field
                  v-model="money.date"
                  label="Action Date"
                  type="date"
                  outlined
                  dense
                  clearable
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions class="pt-0">
          <v-spacer></v-spacer>
          <v-btn color="green" dark @click="addAction()">Save</v-btn>
          <!--                        <v-btn color="red darken-1" dark @click="onClickOutside">{{ $t('close') }}</v-btn>-->
        </v-card-actions>
      </v-card>
    </v-dialog>
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
      user: null,
      actions: null,
      addBalanceModal: false,
      addMoneyModal: false,
      showBalanceStatus: false,
      balance: null,
      actiontypes: [
        {
          value: 0,
          text: "Expanse",
        },
        {
          value: 1,
          text: "income",
        },
      ],
      money: {},
    };
  },

  mounted() {
    this.getUser();
    this.getActions();
    // this.checkBalance();
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
    getUser() {
      this.$http
        .get("/currentUser")
        .then(({ data }) => {
          this.user = data;
          if (this.user.balance == null) {
            this.addBalanceModal = true;
          }
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },
    // checkBalance() {
    //   if (this.user && this.user.balance == null) {
    //     this.addBalanceModal = true;
    //   }
    // },
    setBalance() {
      this.$http
        .post("/setBalance", {
          balance: this.balance,
        })
        .then(({ data }) => {
          this.getUser();
          this.addBalanceModal = false;
          this.showSuccessMessage("Balance added successfully", 3000);
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },
    newAction() {
      this.money = {
        id: Date.now(),
        actiontype: null,
        date: null,
        amount: null,
      };
      this.addMoneyModal = true;
    },
    addAction() {
      this.$http
        .post("/addAction", {
          money: this.money,
        })
        .then(({ data }) => {
          this.getActions();
          this.getUser();
          this.addMoneyModal = false;
          this.showSuccessMessage("Balance added successfully", 3000);
        })
        .catch((error) => {
          console.log(error);
          // this.showErrorMessage();
        });
    },
    getActions() {
      this.$http
        .get("/getActions")
        .then(({ data }) => {
          this.actions = data;
          //data
          // this.showSuccessMessage("Balance added successfully", 3000);
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
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
