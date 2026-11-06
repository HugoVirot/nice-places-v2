<template>
  <header class="sticky-top">
    <div class="container-fluid d-flex align-items-center navbar-dark header-top">
      <!-- ************************** titre et bandes ***********************-->

      <div class="mt-1 d-none d-md-block greenBackground" id="leftstripe"></div>

      <!-- image avec deux bandes (mobiles uniquement) -->
      <img class="d-none" id="twostripes" alt="bandes colorées header mobile"
        src="../../../../public/images/bandes-gauche-header.png" />

      <button class="navbar-toggler greenBackground mobile-menu-toggle" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="d-flex flex-column mx-auto text-center">
        <router-link to="/">
          <img id="logo" alt="logo" src="../../../../public/images/logo.png" />
        </router-link>
        <p id="textelogo" class="mx-auto d-none d-md-block">
          <span class="greentext">sorties nature </span>
          <span class="blueElement">près de chez vous</span>
        </p>
      </div>

      <div class="mt-3 d-none d-md-block" id="rightstripe"></div>
    </div>

    <!-- ************************** navbar ***********************-->

    <nav class="navbar navbar-expand-md navbar-light">
      <div class="container-fluid">
        <!-- liens -->

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <div class="navbar-nav container d-md-flex justify-content-around text-center">
            <router-link to="/" class="navbar-brand">accueil</router-link>
            <router-link to="/carte" class="nav-link">carte</router-link>
            <router-link to="/categories" class="nav-link">catégories</router-link>
            <router-link to="/toplieux" class="nav-link">liste des lieux</router-link>
            <router-link to="/proposerlieu" class="nav-link">proposer un lieu</router-link>

            <!-- si utilisateur connecté : mon compte / mes lieux, si pas connecté : inscription/connexion -->
            <span v-if="userStore.userLoggedIn" class="d-md-flex justify-content-between">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  {{ userStore.name }}
                  <span v-show="userStore.countUnreadNotifications && userStore.countUnreadNotifications > 0
                    ">
                    <i class="text-danger fa-solid fa-bell"></i>
                  </span>
                </a>
                <ul class="dropdown-menu ms-5 ms-md-0 ps-5 ps-md-0" aria-labelledby="navbarDropdownMenuLink">
                  <li>
                    <router-link to="/moncompte" class="nav-link">mon compte</router-link>
                  </li>
                  <li v-if="
                    userStore.countUnreadNotifications && userStore.countUnreadNotifications > 0
                  ">
                    <router-link to="/mesnotifications" class="nav-link">
                      mes notifications
                      <i class="text-danger fa-solid fa-bell m-1"></i>
                      <span class="text-danger p-1">{{ userStore.countUnreadNotifications }} non
                        lue(s)</span>
                    </router-link>
                  </li>
                  <li v-else>
                    <router-link to="/mesnotifications" class="nav-link">mes notifications
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/meslieuxpostes" class="nav-link">mes lieux postés
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/meslieuxfavoris" class="nav-link">mes lieux favoris
                    </router-link>
                  </li>
                  <li v-if="Number(userStore.roleId) === 2 || (!userStore.roleId && userStore.role === 'admin')">
                    <router-link to="/backoffice" class="nav-link">
                      back-office
                    </router-link>
                  </li>
                </ul>
              </li>
              <i class="greenIcon fa-solid fa-right-from-bracket my-auto" @click="logOutUser()"></i>
            </span>

            <span v-else class="d-md-flex justify-content-center">
              <router-link to="/inscription" class="nav-link me-md-3 me-lg-5">inscription</router-link>
              <router-link to="/connexion" class="nav-link">connexion</router-link>
            </span>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useUserStore } from "../../stores/userStore"

const userStore = useUserStore()
const router = useRouter()

const logOutUser = () => {
  // on réinitialise le store
  userStore.$reset()

  // on redirige vers l'accueil
  router.push("/successmessage/home/Déconnexion réussie")
}
</script>

<style>
@font-face {
  font-family: "Cooper";
  src: url("../../Cooper_Black_Regular.ttf") format("truetype");
  font-display: swap;
}

header {
  background-color: white;
  box-shadow: 5px 3px 3px #94d1be;
}

#twostripes {
  display: none;
}

#logo {
  width: 230px;
}

.mobile-menu-toggle {
  display: none;
}

#textelogo {
  font-size: 16px;
}

.dropdown-menu {
  margin-top: 0 !important;
}

a {
  text-decoration: none;
  color: inherit;
}

a:hover {
  color: #94d1be !important;
}

nav .router-link-active,
nav .router-link-exact-active {
  color: grey !important;
}

.dropdown-menu .router-link {
  color: #94d1be;
}

body {
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

html {
  font-size: 80%;
}

h1,
h2,
h3,
h4,
h5,
h6 {
  font-family: "Cooper", system-ui, sans-serif;
}

#leftstripe {
  width: 33vw;
  height: 2vh;
}

#rightstripe {
  width: 33vw;
  background-color: #1c6e8c;
  height: 2vh;
}

.greentext {
  color: #94d1be;
}

.navbar-brand,
.nav-link {
  color: #1c6e8c;
  font-family: "Cooper", system-ui, sans-serif;
}

.navbar-toggler {
  border-radius: 30%;
}

@media screen and (max-width: 768px) {
  .header-top {
    display: grid !important;
    grid-template-columns: 3.5rem 1fr 3.5rem;
    min-height: 4.5rem;
    align-items: center;
    padding: 0 0.75rem;
  }

  .navbar-toggler.mobile-menu-toggle {
    display: inline-flex;
    grid-column: 1;
    grid-row: 1;
    align-self: center;
    align-items: center;
    justify-content: center;
    width: 3.25rem;
    height: 3.25rem;
    margin: 0;
    padding: 0.4rem;
    border: 0;
    background-color: #1c6e8c !important;
  }

  .mobile-menu-toggle .navbar-toggler-icon {
    width: 2rem;
    height: 2rem;
  }

  .header-top > .d-flex.flex-column {
    grid-column: 2;
    grid-row: 1;
    margin: 0 !important;
  }

  #leftstripe,
  #rightstripe,
  #twostripes {
    display: none !important;
  }
}

@media screen and (max-width: 480px) {
  #logo {
    width: 180px;
  }
}
</style>
