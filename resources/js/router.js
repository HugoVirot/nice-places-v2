import { createWebHistory, createRouter } from "vue-router";

const routes = [
  {
    path: "/",
    name: "App",
    component: () => import('./App.vue')
  },
  {
    path: "/carte",
    component: () => import('./components/views/Carte')
  },
  {
    path: "/categories",
    component: () => import('./components/views/Categories')
  },
  {
    path: "/categories/:id",
    component: () => import('./components/views/DetailCategorie')
  },
  {
    path: "/toplieux",
    name: "toplieux",
    component: () => import('./components/views/TopLieux')
  },
  {
    path: "/lieu/:id",
    component: () => import('./components/views/Lieu')
  },
  {
    path: "/proposerlieu",
    component: () => import('./components/views/ProposerLieu')
  },
  {
    path: "/inscription",
    component: () => import('./components/user/Inscription')
  },
  {
    path: "/connexion",
    component: () => import('./components/user/Connexion')
  },
  {
    path: "/moncompte",
    component: () => import('./components/user/MonCompte')
  },
  {
    path: "/meslieuxpostes",
    component: () => import('./components/user/MesLieuxPostes')
  },
  {
    path: "/meslieuxfavoris",
    component: () => import('./components/user/MesLieuxFavoris')
  },
  {
    path: "/backoffice",
    component: () => import('./components/backoffice/BackOffice')
  },
  {
    path: "/successmessage/:nextpage/:message/:lieuid?",
    component: () => import('./components/utilities/SuccessMessage')
  },
  {
    path: "/modifierlieu/:id",
    component: () => import('./components/backoffice/ModifierLieu')
  },
  {
    path: "/modifiercategorie/:id",
    component: () => import('./components/backoffice/ModifierCategorie')
  },
  {
    path: "/modifieravis/:id",
    component: () => import('./components/backoffice/ModifierAvis')
  },
  {
    path: "/modifierimage/:id",
    component: () => import('./components/backoffice/ModifierImage')
  },
  {
    path: "/mesnotifications",
    component: () => import('./components/user/MesNotifications')
  },
  {
    path: "/uploadimages/:id",
    component: () => import('./components/views/UploadImages')
  },
  {
    path: "/contact",
    component: () => import('./components/views/Contact')
  },
  {
    path: "/politique",
    component: () => import('./components/views/Politique')
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;