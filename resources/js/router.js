import { createWebHistory, createRouter } from "vue-router";

const routes = [
  {
    path: "/",
    name: "App",
    component: () => import('./App.vue')
  },
  {
    path: "/carte",
    component: () => import('./components/views/Carte.vue')
  },
  {
    path: "/categories",
    component: () => import('./components/views/Categories.vue')
  },
  {
    path: "/categories/:id",
    component: () => import('./components/views/DetailCategorie.vue')
  },
  {
    path: "/toplieux",
    name: "toplieux",
    component: () => import('./components/views/TopLieux.vue')
  },
  {
    path: "/lieu/:id",
    component: () => import('./components/views/Lieu.vue')
  },
  {
    path: "/proposerlieu",
    component: () => import('./components/views/ProposerLieu.vue')
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
    component: () => import('./components/user/MonCompte.vue')
  },
  {
    path: "/meslieuxpostes",
    component: () => import('./components/user/MesLieuxPostes.vue')
  },
  {
    path: "/meslieuxfavoris",
    component: () => import('./components/user/MesLieuxFavoris.vue')
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
    component: () => import('./components/backoffice/ModifierLieu.vue')
  },
  {
    path: "/modifiercategorie/:id",
    component: () => import('./components/backoffice/ModifierCategorie.vue')
  },
  {
    path: "/modifieravis/:id",
    component: () => import('./components/backoffice/ModifierAvis.vue')
  },
  {
    path: "/modifierimage/:id",
    component: () => import('./components/backoffice/ModifierImage.vue')
  },
  {
    path: "/mesnotifications",
    component: () => import('./components/user/MesNotifications.vue')
  },
  {
    path: "/uploadimages/:id",
    component: () => import('./components/views/UploadImages.vue')
  },
  {
    path: "/contact",
    component: () => import('./components/views/Contact.vue')
  },
  {
    path: "/politique",
    component: () => import('./components/views/Politique.vue')
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;