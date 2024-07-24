<template>
    <div class="p-3">
        <i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-right-to-bracket"></i>
        <h1 class="mt-2">Connexion</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Entrez ici vos informations</div>

                    <div class="card-body">

                        <form @submit.prevent="logIn">

                            <div class="form-group row m-2">
                                <label for="email" class="col-md-4 col-form-label text-md-right">e-mail</label>

                                <div class="col-md-6">
                                    <input v-model="email" id="email" type="email" class="form-control" name="email"
                                        required autocomplete="email">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="password" class="col-md-4 col-form-label text-md-right">mot de passe</label>

                                <div class="col-md-6">
                                    <input v-model="password" id="password" type="password" class="form-control"
                                        name="password" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" id="valider"
                                        class="btn btn-lg greenButton rounded-pill text-light btn-info">
                                        Valider
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { mapActions, mapState } from 'pinia'
import { useUserStore } from '../../stores/userStore'

const email = ref("")
const password = ref("")
const validationErrors = ref("")

const userStore = useUserStore()

const logIn = () => {
    // on initialise la protection CSRF Sanctum via cette route
    axios.get('/sanctum/csrf-cookie')

        .then(() => {
            // on tente la connexion
            axios.post('/api/login', { email: email.value, password: password.value })
                .then(response => {
                    // si elle réussit : stockage des données utilisateur reçues dans le localstorage via le userStore
                    userStore.storeUserData(response.data.data)
                    // récupération des notifications de l'utilisateur qu'on stocke également dans le userStore
                    userStore.getNotifications()
                    // redirection vers un composant affichant le message de succès "vous êtes connecté"             
                    this.$router.push('/successmessage/home/' + response.data.message)
                    // si elle échoue : on affiche la ou les erreurs rencontrée(s)
                }).catch((error) => {
                    validationErrors.value = error.response.data.errors
                })

            // si la requête d'initialisation de la protection CSRF a échoué, on affiche ce message
        }).catch(() => {
            alert("Problème d'authentification'. Merci de recharger la page. Réessayez plus tard ou contactez l'administrateur si le problème persiste.")
        })
}

const getNotifications = () => {
    axios.get('/api/getnotificationsbyuser/' + userStore.id)
        .then(response => {
            // on ne stocke les notifications de l'utilisateur que s'il en possède
            if (response.data.length > 0) { userStore.storeNotifications(response.data) }
        }).catch(() => {
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
}

</script>

<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/plage.jpg);
    background-position: center;
    background-size: cover;
}
</style>
