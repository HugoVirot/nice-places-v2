<template>
    <div class="p-5">
        <i class="fa-solid fa-3x fa-user-check greenIcon"></i>
        <h1 class="mt-2">Mon Compte</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">

        <!-- composant affichant les erreurs de validation des formulaires -->
        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div class="row justify-content-center p-2 p-lg-4">
            <h2 class="mx-auto text-white mb-5">Bienvenue, {{ pseudo }} !</h2>
            <div class="col-md-8 p-1">
                <div class="card">
                    <div class="card-header text-white mb-3 fs-5">Modifier mes infos</div>

                    <form @submit.prevent="sendData">

                        <div class="card-body">

                            <div class="form-group row m-2">
                                <label for="email" class="col-md-4 col-form-label text-md-right">e-mail</label>

                                <div class="col-md-6">
                                    <input v-model="userStore.email" id="email" type="email" class="form-control" name="email"
                                        autocomplete="email">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <p class="mb-2 blueElement mt-2">Département actuel :
                                    <span v-if="departement" class="fs-5"> {{ departement.nom }} ({{ departement.code
                                        }})</span>
                                    <span v-else class="fs-5"> aucun</span>
                                </p>
                                <label for="departement" class="col-md-4 col-form-label text-md-right">changer de
                                    département</label>

                                <div class="col-md-6">
                                    <select id="departement" v-model="userStore.departement" class="form-select mx-auto"
                                        aria-label="filtre" autocomplete="departement">
                                        <option value="null">aucun</option>
                                        <option v-for="department in departements" :value="department">
                                            {{ department.code }} - {{ department.nom }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button id="boutonValider" type="submit" class="btn btn-lg rounded-pill text-light">
                                        Valider
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div class="card-header text-white mt-3 mb-1 fs-5"> Modifier le mot de passe</div>

                        <div class="card-body">

                            <div id="infosMdp" class="form-group row">
                                <div id="passwordHelpBlock" class="mx-auto p-2 m-2 border border-info">
                                    Votre mot de passe doit comporter au moins 8 caractères. Il contenir au moins une
                                    lettre,
                                    un chiffre, une minuscule, une majuscule et un caractère spécial.
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="oldPassword" class="col-md-4 col-form-label text-md-right">mot de passe
                                    actuel</label>

                                <div class="col-md-6">
                                    <input v-model="oldPassword" id="oldPassword" type="password" class="form-control"
                                        name="oldPassword">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="password" class="col-md-4 col-form-label text-md-right">nouveau mot de
                                    passe</label>

                                <div class="col-md-6">
                                    <input v-model="password" @keyup="checkPassword(password)" id="password"
                                        type="password" class="form-control" name="password">
                                </div>
                            </div>

                            <div v-if="passwordTyped" class="container" id="dynamicPasswordCheck">

                                <div class="row">
                                    <p>minimum 8 caractères
                                        <i v-if="eightCharacters"
                                            class="p-2 bg-white rounded-circle greenIcon fa-solid fa-check"></i>
                                        <i v-else class="p-2 bg-white rounded-circle fa-solid fa-xmark"></i>
                                    </p>
                                </div>

                                <div class="row">
                                    <p>minimum 1 lettre
                                        <i v-if="oneLetter"
                                            class="p-2 bg-white rounded-circle greenIcon fa-solid fa-check"></i>
                                        <i v-else class="p-2 bg-white rounded-circle fa-solid fa-xmark"></i>
                                    </p>
                                </div>

                                <div class="row">
                                    <p>minimum 1 chiffre
                                        <i v-if="oneDigit"
                                            class="p-2 bg-white rounded-circle greenIcon fa-solid fa-check"></i>
                                        <i v-else class="p-2 bg-white rounded-circle fa-solid fa-xmark"></i>
                                    </p>
                                </div>

                                <div class="row">
                                    <p>minimum 1 majuscule et 1 minuscule
                                        <i v-if="oneUppercaseOneLowercase"
                                            class="p-2 bg-white rounded-circle greenIcon fa-solid fa-check"></i>
                                        <i v-else class="p-2 bg-white rounded-circle fa-solid fa-xmark"></i>
                                    </p>
                                </div>

                                <div class="row">
                                    <p>minimum 1 caractère spécial
                                        <i v-if="oneSpecialCharacter"
                                            class="p-2 bg-white rounded-circle greenIcon fa-solid fa-check"></i>
                                        <i v-else class="p-2 bg-white rounded-circle fa-solid fa-xmark"></i>
                                    </p>
                                </div>

                            </div>
                        </div>

                        <div v-if="passwordCorrect == true"
                            class="form-group row mx-auto rounded-pill mt-2 mb-4 bg-white w-50">
                            <i class="fa-solid fa-circle-check greenIcon fa-3x p-2 mb-2"></i>
                            <p class="titleIcon">Mot de passe sécurisé</p>
                        </div>

                        <div class="form-group row m-2">
                            <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">confirmez
                                le
                                mot de passe</label>

                            <div class="col-md-6">
                                <input v-model="password_confirmation" id="password_confirmation" type="password"
                                    class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div v-if="passwordCorrect && password == password_confirmation"
                            class="form-group row mx-auto m-2 mt-3 rounded-pill bg-white w-50">
                            <i class="fa-solid fa-circle-check greenIcon fa-3x p-2"></i>
                            <p class="titleIcon">Confirmation OK</p>
                        </div>

                        <div class="form-group row m-3 text-center">
                            <div class="col-md-6 offset-md-3">
                                <button id="boutonValider" type="submit" class="btn btn-lg rounded-pill text-light">
                                    Valider
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="card">
                    <div class="card-header text-white mb-3 fs-5">Supprimer mon compte</div>

                    <form @submit.prevent="deleteAccount">

                        <div class="card-body">

                            <i class="fa-solid fa-triangle-exclamation fa-5x text-danger mb-2"></i>

                            <p class="text-danger">Attention : cette action est irréversible.</p>
                            <p class="text-danger">En supprimant votre compte, vous perdrez tous vos favoris.</p>
                            <p class="text-white">Les lieux et les avis que vous avez postés seront conservés (ils
                                seront anonymisés).</p>
                            <div class="form-group row m-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn rounded-pill text-light btn-danger">
                                        Supprimer mon compte
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useUserStore } from '../../stores/userStore'
import { placesStore } from '../../stores/placesStore'
import { useRouter } from "vue-router"

const userStore = useUserStore()
const placesStore = usePlacesStore()
const router = useRouter()

const oldPassword = ref('')
const password = ref('')
const password_confirmation = ref('')
const passwordTyped = ref(false)
const eightCharacters = ref(false)
const oneLetter = ref(false)
const oneUppercaseOneLowercase = ref(false)
const oneDigit = ref(false)
const oneSpecialCharacter = ref(false)
const passwordCorrect = ref(false)
const validationErrors = ref("")

// vérifier si le mot de passe réunit les critères de sécurité
const checkPassword = password => {

    passwordTyped.value = true

    if (password.length >= 8) {
        eightCharacters.value = true
    } else {
        eightCharacters.value = false
    }

    if (password.match(/[a-z]/)) {
        oneLetter.value = true
    } else {
        oneLetter.value = false
    }

    if (password.match(/(?=.*[a-z])(?=.*[A-Z])/)) {
        oneUppercaseOneLowercase.value = true
    } else {
        oneUppercaseOneLowercase.value = false
    }

    if (password.match(/\d/)) {
        oneDigit.value = true
    } else {
        oneDigit.value = false;
    }

    if (password.match(/[!@#$%^&*?]/)) {
        oneSpecialCharacter.value = true
    } else {
        oneSpecialCharacter.value = false;
    }

    if (eightCharacters && oneLetter && oneUppercaseOneLowercase && oneDigit && oneSpecialCharacter) {
        passwordCorrect.value = true
    }
},

// on envoie les modifs pour les sauvegarder en bdd puis on redirige
const sendData = () => {
    axios.put('/api/users/' + userStore.id, {
        email: userStore.email, departement_id: userStore.departement.id, oldPassword: oldPassword,
        password: password, password_confirmation: password_confirmation
    }).then(response => {
        this.storeUserData(response.data.data)
        router.push('/successmessage/lastpage/' + response.data.message)
    }).catch(error => {
        if (error.response) {
            validationErrors.value = error.response.data.errors;
        }
    })
}

// suppression du compte utilisateur
const deleteAccount = () => {
    axios.delete('/api/users/' + userStore.id)
        .then((response) => {
            // suppression compte fonctionne (plus dans bdd) mais il ne se passe rien ensuite
            // => pas de déconnexion, mais le user n'existe plus. On reste connecté.
            // à corriger
            logOutUser()
            router.push('/successmessagehome/' + response.data.message)
        })
        .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
}

// déconnecter l'utilisateur (utilisé après la suppression de compte)
logOutUser() {
    // on réinitialise le store 
    userStore.$reset()

    // on redirige vers l'accueil
    router.push('/successmessagehome/Déconnexion réussie')
}

</script>

<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/sunset2.jpg);
    background-position: center;
    background-size: cover;
}

.fa-xmark {
    color: red
}

#passwordHelpBlock {
    max-width: 600px;
}

#boutonValider {
    background-color: #1C6E8C;
    color: white;
}
</style>