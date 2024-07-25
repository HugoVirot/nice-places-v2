<template>
  <header class="sticky-top">
    <div class="container-fluid pt-1 d-flex align-items-center navbar-dark">
      <!-- ************************** titre et bandes ***********************-->

      <div class="mt-1 d-none d-md-block greenBackground" id="leftstripe"></div>

      <!-- image avec deux bandes (mobiles uniquement) -->
      <img class="d-block mt-3 d-md-none" id="twostripes" alt="bandes colorées header mobile"
        src="../../../../public/images/bandes-gauche-header.png" />

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

      <!-- hamburger -->

      <button class="navbar-toggler greenBackground mt-3 d-md-none my-auto" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
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
            <router-link to="/toplieux" class="nav-link">top 100 des lieux</router-link>
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
                  <li v-if="userStore.role == 'admin'">
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
  src: "./Cooper_Black_Regular.ttf" format("truetype")
}

header {
  background-color: white;
  box-shadow: 5px 3px 3px #94d1be;
}

#twostripes {
  width: 15vw;
}

#logo {
  width: 260px;
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
  font-family: "Cooper", Arial;
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
}

.navbar-toggler {
  border-radius: 30%;
}

@media screen and (max-width: 480px) {
  #logo {
    width: 180px;
  }
}
</style>
