import Vue from "vue";
import VueRouter from "vue-router";
import Login from "@/views/auth/Login.vue";
import $store from "@/store";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "home",
    meta: {
      guest: true,
    },
    redirect: {
      name: "auth.login",
    },
  },
  {
    path: "/todos",
    name: "todos",
    meta: {
      auth: true,
    },
    component: () =>
      import(/* webpackChunkName: "todos" */ "../views/Todos.vue"),
  },
  // {
  //   path: "quiz/show/:documentTemplateId",
  //   component: () => import("@/views/quiz/admin/Index"),
  //   meta: {
  //     auth: true,
  //   },
  // },
  {
    path: "/admin",
    component: () => import("@/layouts/DefaultLayout"),
    children: [
      {
        path: "index",
        component: () => import("@/views/quiz/admin/Index"),
        meta: {
          auth: true,
        },
      },
    ],
  },
  {
    path: "/quizzes",
    component: () => import("@/layouts/DefaultLayout"),
    children: [
      {
        path: "index",
        component: () => import("@/views/quiz/Quizzes"),
        meta: {
          auth: true,
        },
      },
      {
        path: "attempt",
        component: () => import("@/views/quiz/Attempt"),
        meta: {
          auth: true,
        },
      },
    ],
  },
  // {
  //   path: "/quiz",
  //   name: "quiz",
  //   meta: {
  //     auth: true,
  //   },
  //   component: () =>
  //     import(/* webpackChunkName: "todos" */ "../views/quiz/Quizzes.vue"),
  // },
  {
    path: "/student",
    name: "student",
    meta: {
      auth: true,
    },
    component: () =>
      import(/* webpackChunkName: "todos" */ "../views/Student.vue"),
  },

  // Auth
  {
    path: "/login",
    name: "auth.login",
    meta: {
      guest: true,
    },
    component: Login,
  },
  {
    path: "/register",
    name: "auth.register",
    meta: {
      guest: true,
    },
    component: () =>
      import(/* webpackChunkName: "register" */ "../views/auth/Register.vue"),
  },
  {
    path: "/404",
    name: "pageNotFound",
    meta: {
      auth: true,
    },
    component: () =>
      import(
        /* webpackChunkName: "pageNotFound" */ "../views/PageNotFound.vue"
      ),
  },
  {
    path: "*",
    meta: {
      auth: true,
    },
    redirect: "/404",
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

router.beforeEach((to, from, next) => {
  const loggedIn = localStorage.getItem("user");

  if (to.matched.some((record) => record.meta.auth) && !loggedIn) {
    next("/login");
    return;
  }

  if (to.matched.some((record) => record.meta.guest) && loggedIn) {
    next("/todos");
    return;
  }

  next();
});

router.afterEach((to, from) => {
  const path = to.path.substr(1).split("/")[0];

  if (path === "login" || path === "register") {
    $store.commit("SET_LAYOUT", "auth-layout");
  } else {
    $store.commit("SET_LAYOUT", "app-layout");
  }
});

export default router;
