<script>
import axios from "axios";
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { mapState } from 'pinia'
import { useUserStore } from '../../stores/userStore'
import { useLieuxStore } from '../../stores/lieuxStore'

export default {

    computed: {
        ...mapState(useUserStore, [
            'pseudo', 'userLoggedIn', 'role', 'id'
        ]),

        ...mapState(useLieuxStore, ['categories', 'departements', 'storeNewLieu'])
    },

    data() {
        return {
            nom: "",
            description: "",
            images: [],
            latitude: "",
            longitude: "",
            categorie: "",
            note: "",
            temps: "",
            difficulte: "",
            kilometres: "",
            departement: "",
            adresse: "",
            code_postal: "",
            ville: "",
            formData: new FormData(),
            validationErrors: ""
        }
    },

    components: { ValidationErrors },

    methods: {

        // poste le nouveau lieu pour le sauvegarder en base de données
        sendData() {

            this.formData.append("nom", this.nom);
            this.formData.append("description", this.description);
            this.formData.append("latitude", this.latitude);
            this.formData.append("longitude", this.longitude);
            this.formData.append("categorie", this.categorie);
            this.formData.append("note", this.note);
            this.formData.append("temps", this.temps);
            this.formData.append("difficulte", this.difficulte);
            this.formData.append("kilometres", this.kilometres);
            this.formData.append("departement", this.departement);
            this.formData.append("adresse", this.adresse);
            this.formData.append("code_postal", this.code_postal);
            this.formData.append("ville", this.ville);
            this.formData.append("user_id", this.id);

            axios.post('/api/lieus', this.formData)
                .then((response) => {
                    // on ajoute le nouveau lieu à la liste des lieux du store
                    // (cela évite de récupérer la liste complète via un appel API pour un lieu)
                    // il ne sera pas visible tout de suite mais apparaîtra dans "mes lieux postés"
                    this.storeNewLieu(response.data.data)

                    let message = response.data.message
                    let lieu = response.data.data

                    // si utilisateur normal, on lui sauvegarde une notification en base de données
                    // if (this.role !== "admin") {
                    this.createNotification(lieu.id)
                    // }

                    this.$router.push('/successmessageuploadimages/' + message + '/' + lieu.id)
                }).catch((error) => {
                    this.validationErrors = error.response.data.errors;
                })
        },

        // on sauvegarde une notification en base de données pour indiquer à l'utilisateur
        // que son lieu a bien été proposé et est mis en attente
        createNotification(lieuId) {
            let titre = `Votre lieu ${this.nom} a bien été proposé !`;
            let message = `<p>Merci ${this.pseudo} !<p><br> 
            <i style="color:#94D1BE" class="mx-auto fa-solid fa-paper-plane fa-5x p-2"></i><br>
            <p>Votre lieu, ${this.nom}, a bien été proposé.<br>
            Il a été mis en attente et va être vérifié par l'administrateur.<br>
            Ce dernier reviendra alors vers vous.<br>
            A très bientôt.</p>`

            axios.post('/api/notifications', { titre: titre, message: message, user_id: this.id, lieu_id: lieuId },)
                .then(response => console.log(response.data.message))
                .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    }
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

                    <a href="https://www.lecoindunet.com/coordonnees-gps-lieu-google-maps" target="_blank">
                        <button class="btn blueButton my-3">
                            méthode pour trouver la latitude et la longitude d'un endroit (PC/smartphone/tablette)
                        </button>
                    </a>

                    <div class="card-body">

                        <form @submit.prevent="sendData" enctype="multipart/form-data">

                            <div class="form-group row m-2">
                                <label for="nom" class="col-md-4 col-form-label text-md-right">nom</label>

                                <div class="col-md-6">
                                    <input v-model="nom" id="nom" type="text" class="form-control" name="nom" required
                                        autocomplete="nom" autofocus>
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
                                <label for="categorie" class="col-md-4 col-form-label text-md-right">catégorie</label>
                                <div class="col-md-6">
                                    <select required v-model="categorie" class="form-select" aria-label="categorie" name="categorie">
                                        <option v-for="categorie in categories" :key="categorie.id"
                                            :value="categorie.id">{{ categorie.nom }}</option>
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
                                <label for="temps" class="col-md-4 col-form-label text-md-right">temps moyen en
                                    heures</label>

                                <div class="col-md-6">
                                    <input min="1" max="24" v-model="temps" id="temps" type="number"
                                        class="form-control" name="temps" required autocomplete="temps">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="temps" class="col-md-4 col-form-label text-md-right">niveau de
                                    difficulté</label>

                                <div class="col-md-6">
                                    <select required v-model="difficulte" class="form-select" aria-label="difficulte" name="difficulte">
                                        <option selected value="famille">famille</option>
                                        <option value="amateur">amateur</option>
                                        <option value="sportif">sportif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="temps" class="col-md-4 col-form-label text-md-right">distance moyenne en
                                    km (facultatif)</label>

                                <div class="col-md-6">
                                    <input min="1" max="150" v-model="kilometres" id="kilometres" type="number"
                                        class="form-control" name="kilometres" autocomplete="kilometres">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label class="col-md-4 col-form-label text-md-right"
                                    for="departement">département</label>
                                <select id="departement" name="departement" required v-model="departement" class="form-select w-50 mx-auto"
                                    aria-label="filtre">
                                    <option v-for="(departement, index) in departements" :selected="index == 0"
                                        :value="departement.id">{{
                                            departement.code
                                        }} - {{ departement.nom }}</option>
                                </select>
                            </div>

                            <div class="form-group row m-2">
                                <label for="adresse" class="col-md-4 col-form-label text-md-right">adresse</label>

                                <div class="col-md-6">
                                    <input v-model="adresse" id="adresse" type="text" class="form-control"
                                        name="adresse" required autocomplete="adresse">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="code_postal" class="col-md-4 col-form-label text-md-right">code
                                    postal</label>

                                <div class="col-md-6">
                                    <input v-model="code_postal" id="code_postal" type="text" class="form-control"
                                        name="code_postal" required autocomplete="code_postal">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="ville" class="col-md-4 col-form-label text-md-right">ville</label>

                                <div class="col-md-6">
                                    <input v-model="ville" id="ville" type="text" class="form-control" name="ville"
                                        required autocomplete="ville">
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
        <p class="mb-2">Vous devez être connecté pour proposer un lieu.</p>
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