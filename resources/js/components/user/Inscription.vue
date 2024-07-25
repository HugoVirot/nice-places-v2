<template>
    <div class="p-5">
        <i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-user-plus"></i>
        <h1 class="mt-2">Inscription</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />
        <div class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Quelques secondes suffisent !</div>

                    <div class="card-body">

                        <form @submit.prevent="sendData">

                            <div class="form-group row m-2 mb-3">
                                <div class="col-md-9">
                                    <label for="politique">J'ai lu et j'accepte la
                                        <router-link to="/politique">politique de confidentialité</router-link>
                                    </label>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" v-model="politique" id="politique" name="politique">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="pseudo" class="col-md-4 col-form-label text-md-right">pseudo</label>

                                <div class="col-md-6">
                                    <input v-model="pseudo" id="pseudo" type="text" class="form-control" name="pseudo"
                                        required autocomplete="pseudo" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="email" class="col-md-4 col-form-label text-md-right">e-mail</label>

                                <div class="col-md-6">
                                    <input v-model="email" id="email" type="email" class="form-control" name="email"
                                        required autocomplete="email">
                                </div>
                            </div>
                            <div id="emailHelp" class="form-text">Nous ne partagerons jamais votre e-mail avec des
                                tiers.
                            </div>

                            <div class="form-group row m-2">
                                <label for="department" class="col-md-4 col-form-label text-md-right">département
                                    (facultatif)</label>

                                <div class="col-md-6">
                                    <select id="department" v-model="department" name="department"
                                        class="form-select mx-auto" aria-label="filtre" autocomplete="department">
                                        <option v-for="department in departments" :value="department.id">
                                            {{ department.code }} - {{ department.nom }}
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div id="infosMdp" class="form-group row my-2">
                                <div id="passwordHelpBlock" class="mx-auto p-2 m-2 border border-info">
                                    Votre mot de passe doit comporter au moins 8 caractères. Il contenir au moins une
                                    lettre,
                                    un chiffre, une minuscule, une majuscule et un caractère spécial.
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="password" class="col-md-4 col-form-label text-md-right">mot de passe</label>

                                <div class="col-md-6">
                                    <input v-model="password" @keyup="checkPassword(password)" id="password"
                                        type="password" class="form-control" name="password" required
                                        autocomplete="new-password">
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

                            <div v-if="passwordCorrect == true"
                                class="form-group row mx-auto rounded-pill mt-2 mb-4 bg-white w-50">
                                <i class="fa-solid fa-circle-check greenIcon fa-3x p-2 mb-2"></i>
                                <p class="titleIcon">Mot de passe sécurisé</p>
                            </div>

                            <div class="form-group row m-2">
                                <label for="password_confirmation"
                                    class="col-md-4 col-form-label text-md-right">confirmez
                                    le
                                    mot de passe</label>

                                <div class="col-md-6">
                                    <input v-model="password_confirmation" id="password_confirmation" type="password"
                                        class="form-control" name="password_confirmation" required
                                        autocomplete="new-password">
                                </div>
                            </div>

                            <div v-if="passwordCorrect && password == password_confirmation"
                                class="form-group row mx-auto m-2 mt-3 rounded-pill bg-white w-50">
                                <i class="fa-solid fa-circle-check greenIcon fa-3x p-2"></i>
                                <p class="titleIcon">Confirmation OK</p>
                            </div>

                            <div v-if="politique" class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn btn-lg greenButton rounded-pill text-white">
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

    <div class="modal bg-success" id="successModal" ref="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Félicitations !</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Inscription réussie ! </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useLieuxStore } from "../../stores/lieuxStore.js"

const placesStore = usePlacesStore()

const pseudo = ref('')
const email = ref('')
const department = ref('')
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
const politique = ref(false)

const sendData = () => {

    axios.post('/api/register', {
        pseudo: this.pseudo, email: this.email, department: this.department,
        password: this.password, password_confirmation: this.password_confirmation
    })
        .then(response => {

            let message = response.data.message
            // on enregistre une notification de confirmation à destination de l'utilisateur
            this.createNotification(response.data.data.id)

            this.$router.push('/successmessage/connexion/' + message);
        })
        .catch((error) => {
            this.validationErrors = error.response.data.errors;
        })
},

const createNotification = userId => {

    let titre = `Bienvenue sur Nice Places ${this.pseudo} !`;
    let message = `<p class="text-secondary">Bonjour ${this.pseudo} et bienvenue sur Nice Places !<br>
            Votre inscription est réussie.<br>
            <i style="color:#94D1BE" class="mx-auto my-3 fa-solid fa-door-open fa-5x"></i><br>
            Venez découvrir la France et partager vos lieux préférés avec nous !<br>
            A très bientôt.</p>
            <p class="text-end">L'administrateur.</p>`

    axios.post('/api/notifications', { titre: titre, message: message, user_id: userId, lieu_id: null })
        .then(response => console.log(response.data.message))
        .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
},

const checkPassword = password => {

    this.passwordTyped = true

    if (password.length >= 8) {
        this.eightCharacters = true
    } else {
        this.eightCharacters = false
    }

    if (password.match(/[a-z]/)) {
        this.oneLetter = true
    } else {
        this.oneLetter = false
    }

    if (password.match(/(?=.*[a-z])(?=.*[A-Z])/)) {
        this.oneUppercaseOneLowercase = true
    } else {
        this.oneUppercaseOneLowercase = false
    }

    if (password.match(/\d/)) {
        this.oneDigit = true
    } else {
        this.oneDigit = false;
    }

    if (password.match(/[!@#$%^&*?]/)) {
        this.oneSpecialCharacter = true
    } else {
        this.oneSpecialCharacter = false;
    }

    if (this.eightCharacters && this.oneLetter && this.oneUppercaseOneLowercase && this.oneDigit && this.oneSpecialCharacter) {
        this.passwordCorrect = true
    }
}
</script>

<style scoped>
.container-fluid {
    background-image: linear-gradient(rgba(132, 247, 192, 0.4),
            rgba(7, 117, 230, 0.3)), url(../../../../public/images/mountain_river.jpg);
    background-position: center;
    background-size: cover;
}

#passwordHelpBlock {
    max-width: 600px;
}

.fa-xmark {
    color: red
}
</style>
