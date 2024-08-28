<script setup>
import axios from "axios";
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useUserStore } from '../../stores/userStore'
import { usePlacesStore } from "../../stores/placesStore";
import { useRouter } from "vue-router";

const userStore = useUserStore()
const placesStore = usePlacesStore()
const router = useRouter()
// computed: {
//     ...mapState(useUserStore, [
//         'name', 'userLoggedIn', 'role', 'id'
//     ]),

//     ...mapState(useLieuxStore, ['categorys', 'departments', 'storeNewLieu'])
// },

const name = ref("")
const description = ref("")
const latitude = ref("")
const longitude = ref("")
const category = ref("")
const note = ref("")
const time = ref("")
const difficulty = ref("")
const kilometres = ref("")
const department = ref("")
const adress = ref("")
const postcode = ref("")
const city = ref("")
const formData = new FormData()
const validationErrors = ref("")

// poste le nouveau place pour le sauvegarder en base de données
const sendData = () => {

    formData.append("name", this.name);
    formData.append("description", this.description);
    formData.append("latitude", this.latitude);
    formData.append("longitude", this.longitude);
    formData.append("category", this.category);
    formData.append("note", this.note);
    formData.append("time", this.time);
    formData.append("difficulty", this.difficulty);
    formData.append("kilometres", this.kilometres);
    formData.append("department", this.department);
    formData.append("adress", this.adress);
    formData.append("postcode", this.postcode);
    formData.append("city", this.city);
    formData.append("user_id", this.id);

    axios.post('/api/places', formData)
        .then((response) => {
            // on ajoute le nouveau place à la liste des placex du store
            // (cela évite de récupérer la liste complète via un appel API pour un place)
            // il ne sera pas visible tout de suite mais apparaîtra dans "mes placex postés"
            placesStore.storeNewPlace(response.data.data)

            let message = response.data.message
            let place = response.data.data

            // si utilisateur normal, on lui sauvegarde une notification en base de données
            // if (this.role !== "admin") {
            createNotification(place.id)
            // }

            router.push('/successmessageuploadimages/' + message + '/' + place.id)
        }).catch((error) => {
            validationErrors.value = error.response.data.errors;
        })
},

// on sauvegarde une notification en base de données pour indiquer à l'utilisateur
// que son place a bien été proposé et est mis en attente
const createNotification = placeId => {
    let title = `Votre place ${name} a bien été proposé !`;
    let message = `<p>Merci ${userStore.name} !<p><br> 
            <i style="color:#94D1BE" class="mx-auto fa-solid fa-paper-plane fa-5x p-2"></i><br>
            <p>Votre place, ${name}, a bien été proposé.<br>
            Il a été mis en attente et va être vérifié par l'administrateur.<br>
            Ce dernier reviendra alors vers vous.<br>
            A très bientôt.</p>`

    axios.post('/api/notifications', { title: title, message: message, user_id: userStore.id, place_id: placeId },)
        .then(response => console.log(response.data.message))
        .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
}


</script>

<template>

    <div class="p-5">
        <i class="mx-auto greenIcon fa-3x fa-solid fa-paper-plane"></i>
        <h1 class="mt-2">Proposer un lieu</h1>
    </div>

    <div v-if="userLoggedIn" class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Partagez vos coups de coeur avec nous !</div>

                    <a href="https://www.lecoindunet.com/coordonnees-gps-place-google-maps" target="_blank">
                        <button class="btn blueButton my-3">
                            méthode pour trouver la latitude et la longitude d'un endroit (PC/smartphone/tablette)
                        </button>
                    </a>

                    <div class="card-body">

                        <form @submit.prevent="sendData" enctype="multipart/form-data">

                            <div class="form-group row m-2">
                                <label for="name" class="col-md-4 col-form-label text-md-right">name</label>

                                <div class="col-md-6">
                                    <input v-model="name" id="name" type="text" class="form-control" name="name"
                                        required autocomplete="name" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="description"
                                    class="col-md-4 col-form-label text-md-right">description</label>

                                <div class="col-md-6">
                                    <textarea v-model="description" id="description" class="form-control"
                                        name="description" required autocomplete="description"></textarea>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="latitude" class="col-md-4 col-form-label text-md-right">latitude</label>
                                <div class="col-md-6">
                                    <input v-model="latitude" id="latitude" type="text" class="form-control"
                                        name="latitude" required autocomplete="latitude">
                                </div>
                            </div>
                            <div class="form-text">entre -90 et 90. Partie décimale : maximum 7 chiffres.</div>

                            <div class="form-group row m-2">
                                <label for="longitude" class="col-md-4 col-form-label text-md-right">longitude</label>
                                <div class="col-md-6">
                                    <input v-model="longitude" id="longitude" type="text" class="form-control"
                                        name="longitude" required autocomplete="longitude">
                                </div>
                            </div>
                            <div class="form-text">entre -180 et 180. Partie décimale : maximum 7 chiffres.</div>

                            <div class="form-group row m-2">
                                <label for="category" class="col-md-4 col-form-label text-md-right">catégorie</label>
                                <div class="col-md-6">
                                    <select required v-model="category" class="form-select" aria-label="category"
                                        name="category">
                                        <option v-for="category in categorys" :key="category.id" :value="category.id">{{
                                            category.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="note" class="col-md-4 col-form-label text-md-right">votre note sur
                                    10</label>

                                <div class="col-md-6">
                                    <input v-model="note" id="note" min="0" max="10" type="number" class="form-control"
                                        name="note" required autocomplete="note">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="time" class="col-md-4 col-form-label text-md-right">time moyen en
                                    heures</label>

                                <div class="col-md-6">
                                    <input min="1" max="24" v-model="time" id="time" type="number" class="form-control"
                                        name="time" required autocomplete="time">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="time" class="col-md-4 col-form-label text-md-right">niveau de
                                    difficulté</label>

                                <div class="col-md-6">
                                    <select required v-model="difficulty" class="form-select" aria-label="difficulty"
                                        name="difficulty">
                                        <option selected value="famille">famille</option>
                                        <option value="amateur">amateur</option>
                                        <option value="sportif">sportif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="time" class="col-md-4 col-form-label text-md-right">distance moyenne en
                                    km (facultatif)</label>

                                <div class="col-md-6">
                                    <input min="1" max="150" v-model="kilometres" id="kilometres" type="number"
                                        class="form-control" name="kilometres" autocomplete="kilometres">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label class="col-md-4 col-form-label text-md-right"
                                    for="department">département</label>
                                <select id="department" name="department" required v-model="department"
                                    class="form-select w-50 mx-auto" aria-label="filtre">
                                    <option v-for="(department, index) in departments" :selected="index == 0"
                                        :value="department.id">{{
                                            department.code
                                        }} - {{ department.name }}</option>
                                </select>
                            </div>

                            <div class="form-group row m-2">
                                <label for="adress" class="col-md-4 col-form-label text-md-right">adress</label>

                                <div class="col-md-6">
                                    <input v-model="adress" id="adress" type="text" class="form-control" name="adress"
                                        required autocomplete="adress">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="postcode" class="col-md-4 col-form-label text-md-right">code
                                    postal</label>

                                <div class="col-md-6">
                                    <input v-model="postcode" id="postcode" type="text" class="form-control"
                                        name="postcode" required autocomplete="postcode">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="city" class="col-md-4 col-form-label text-md-right">city</label>

                                <div class="col-md-6">
                                    <input v-model="city" id="city" type="text" class="form-control" name="city"
                                        required autocomplete="city">
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit"
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
    <div v-else class="container">
        <p class="mb-2">Vous devez être connecté pour proposer un place.</p>
        <router-link to="/connexion"><button class="btn btn-lg greenButton mb-3 rounded-pill">Se connecter</button>
        </router-link>

        <p class="mb-2">Pas encore inscrit ?</p>
        <router-link to="/inscription"><button class="btn btn-lg greenButton rounded-pill">Créer un compte</button>
        </router-link>
    </div>

</template>

<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/riviere.jpg);
    background-position: center;
    background-size: cover;
}
</style>