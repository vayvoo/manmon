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

        <v-progress-linear
          v-if="!loaded"
          color="primary"
          indeterminate
          rounded
          height="4"
        ></v-progress-linear>

        <div class="d-flex justify-space-around">
          <div class="d-flex flex-column align-center">
            <p class="income-text mb-n1" style="font-size: 32px">
              + {{ allIncome }} so'm
            </p>
            <p class="income-text">Kirim</p>
          </div>
          <div class="d-flex flex-column align-center">
            <p class="expance-text mb-n1" style="font-size: 32px">
              - {{ allExpance }} so'm
            </p>
            <p class="expance-text">Chiqim</p>
          </div>
        </div>
        <v-card class="mt-2 mb-10">
          <v-list>
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
                  <div class="d-flex justify-space-between">
                    <v-list-item-title>
                      {{ todo.description }}
                    </v-list-item-title>
                    <div>
                      <v-list-item-subtitle
                        v-if="todo.type == 0"
                        class="font-weight-bold"
                      >
                        - {{ todo.amount }} so'm
                      </v-list-item-subtitle>
                      <v-list-item-subtitle v-else class="font-weight-bold">
                        {{ todo.amount }} so'm
                      </v-list-item-subtitle>
                      <v-list-item-subtitle>
                        {{ todo.date }}
                      </v-list-item-subtitle>
                    </div>
                  </div>
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
                      <v-list-item @click="deleteAction(todo)">
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
          <span class="headline">Add Action</span>
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
                  v-model="money.description"
                  label="Description"
                  type="text"
                  solo
                  dense
                  clearable
                ></v-text-field>
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
      user: null,
      actions: null,
      addBalanceModal: false,
      addMoneyModal: false,
      showBalanceStatus: false,
      balance: null,
      loaded: false,
      allIncome: 0,
      allExpance: 0,
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
  },

  methods: {
    getUser() {
      this.allIncome = 0;
      this.allExpance = 0;
      this.$http
        .get("/currentUser")
        .then(({ data }) => {
          this.user = data[0];
          this.actions = data[0].actions;
          this.actions.filter((v) => {
            if (v.type == 1) {
              this.allIncome += parseInt(v.amount);
            } else {
              this.allExpance += parseInt(v.amount);
            }
          });
          this.loaded = true;
          if (this.user.balance == null) {
            this.addBalanceModal = true;
          }
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },
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
          this.getUser();
          this.addMoneyModal = false;
          this.showSuccessMessage("Balance added successfully", 3000);
        })
        .catch((error) => {
          console.log(error);
          // this.showErrorMessage();
        });
    },

    deleteAction(todo) {
      this.$http
        .delete("/deleteAction/" + todo.id)
        .then(({ data }) => {
          this.getUser();

          this.showSuccessMessage("Action has been deleted");
        })
        .catch((error) => {
          console.log(error);
          this.showErrorMessage();
        });
    },

    edit(todo) {
      this.addMoneyModal = true;
      this.money = Object.assign({}, todo);
    },
  },
};
</script>

<style lang="css" scoped>
.input-errors >>> .v-input__control > .v-input__slot:before {
  border-color: #f56c6c !important;
}
.income-text {
  color: rgba(47, 195, 195, 0.721);
}
.expance-text {
  color: red;
}
</style>
