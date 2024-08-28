import { createWebHistory, createRouter } from "vue-router";

const routes = [
  {
    path: "/",
    name: "App",
    component: () => import('./App.vue')
  },
  {
    path: "/carte",
    component: () => import('./components/views/MapScreen.vue')
  },
  {
    path: "/categories",
    component: () => import('./components/views/Categories.vue')
  },
  {
    path: "/categories/:id",
    component: () => import('./components/views/Category.vue')
  },
  {
    path: "/toplieux",
    name: "toplieux",
    component: () => import('./components/views/TopPlaces.vue')
  },
  {
    path: "/lieu/:id",
    component: () => import('./components/views/Place.vue')
  },
  {
    path: "/proposerlieu",
    component: () => import('./components/views/CreatePlace.vue')
  },
  {
    path: "/inscription",
    component: () => import('./components/user/Inscription.vue')
  },
  {
    path: "/connexion",
    component: () => import('./components/user/Connection.vue')
  },
  {
    path: "/moncompte",
    component: () => import('./components/user/Account.vue')
  },
  {
    path: "/meslieuxpostes",
    component: () => import('./components/user/MyPostedPlaces.vue')
  },
  {
    path: "/meslieuxfavoris",
    component: () => import('./components/user/MyFavoritePlaces.vue')
  },
  {
    path: "/backoffice",
    component: () => import('./components/backoffice/BackOffice.vue')
  },
  {
    path: "/successmessage/:nextpage/:message/:lieuid?",
    component: () => import('./components/utilities/SuccessMessage.vue')
  },
  {
    path: "/modifierlieu/:id",
    component: () => import('./components/backoffice/EditPlace.vue')
  },
  {
    path: "/modifiercategorie/:id",
    component: () => import('./components/backoffice/EditCategory.vue')
  },
  {
    path: "/modifieravis/:id",
    component: () => import('./components/backoffice/EditReview.vue')
  },
  {
    path: "/modifierimage/:id",
    component: () => import('./components/backoffice/EditImage.vue')
  },
  {
    path: "/mesnotifications",
    component: () => import('./components/user/Notifications.vue')
  },
  {
    path: "/uploadimages/:id",
    component: () => import('./components/views/ImageUpload.vue')
  },
  {
    path: "/contact",
    component: () => import('./components/views/Contact.vue')
  },
  {
    path: "/politique",
    component: () => import('./components/views/Policy.vue')
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;