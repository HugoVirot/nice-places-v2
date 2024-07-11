<template>
    <div class="p-3">
        <i class="fa-3x greenIcon fa-solid fa-pen-to-square"></i>
        <h1 class="mt-2">Modifier {{ lieu.nom }}</h1>
        <h2 v-if="role == 'admin' && lieu">Posté par {{ lieu.user.pseudo }}</h2>
        <div v-if="lieu">
            <div v-if="statut == 'validé'" class="mx-auto bg-success text-white w-25">validé</div>
            <div v-else-if="statut == 'en attente'" class="mx-auto bg-info w-25">en attente de validation
            </div>
            <div v-else-if="statut == 'à modifier'" class="mx-auto bg-warning w-25">à modifier pour être validé
            </div>
            <div v-else class="mx-auto bg-danger w-25">refusé
            </div>
        </div>
    </div>

    <div class="container-fluid p-3 p-lg-5" :style="`background-image: url(/images/${
        lieu.image_mise_en_avant && lieu.image_mise_en_avant[0] ? lieu.image_mise_en_avant[0].nom : 'rocks.jpg'
        }); background-position: center; background-size: cover;`">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div v-if="lieu !== ''" class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Entrez les nouvelles informations</div>

                    <div class="card-body">

                        <form @submit.prevent="saveChanges">

                            <div v-if="role == 'admin'" class="form-group row m-2">
                                <label for="statut" class="col-md-4 col-form-label text-md-right">modifier le
                                    statut</label>

                                <div class="col-md-6">
                                    <select required v-model="statut" class="form-select" aria-label="statut">
                                        <option value="validé">validé</option>
                                        <option value="en attente">en attente</option>
                                        <option value="à modifier">à modifier</option>
                                        <option value="refusé">refusé</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="statut == 'à modifier' || statut == 'refusé'" class="form-group row m-2">
                                <label for="commentaire" class="col-md-4 col-form-label text-md-right">changements à
                                    effectuer ou raison du refus</label>

                                <div class="col-md-6">
                                    <input v-model="commentaire" id="commentaire" type="text" class="form-control"
                                        name="commentaire" required autocomplete="commentaire" autofocus>
                                </div>
                            </div>

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
                            <div class="form-text mb-2">entre -180 et 180. Partie décimale : maximum 7 chiffres.</div>

                            <!-- <p>Catégorie actuelle : {{ categorie.nom }}</p> -->

                            <div class="form-group row m-2">
                                <label for="categorie_id"
                                    class="col-md-4 col-form-label text-md-right">catégorie</label>

                                <div class="col-md-6">
                                    <select v-model="categorie_id" class="form-select" aria-label="categorie_id">
                                        <option v-for="categorie in categories" :value="categorie.id">
                                            {{ categorie.nom }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- seul l'Admin peut changer la note du lieu -->
                            <div v-if="role == 'admin'" class="form-group row m-2">
                                <label for="note" class="col-md-4 col-form-label text-md-right">note sur
                                    10</label>

                                <div class="col-md-6">
                                    <input v-model="note" id="note" min="0" max="10" type="number" step="0.1"
                                        class="form-control" name="note" required autocomplete="note">
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
                                    <select required v-model="difficulte" class="form-select" aria-label="difficulte">
                                        <option :selected="difficulte == 'famille' ? 'selected' : ''" value="famille">
                                            famille</option>
                                        <option :selected="difficulte == 'amateur' ? 'selected' : ''" value="amateur">
                                            amateur</option>
                                        <option :selected="difficulte == 'sportif' ? 'selected' : ''" value="sportif">
                                            sportif</option>
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
                                    for="departement_id">département</label>
                                <select id="departement_id" required v-model="departement_id" class="form-select w-50"
                                    aria-label="filtre" autocomplete="departement_id">
                                    <option v-for="department in departements" :value="department.id">
                                        {{ department.code }} - {{ department.nom }}</option>
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
</template>

<script>
import axios from 'axios'
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useUserStore } from "../../stores/userStore.js";
import { useLieuxStore } from '../../stores/lieuxStore';
import { mapState, mapWritableState } from 'pinia';
import { mapActions } from 'pinia';

export default {

    computed: {
        ...mapState(useUserStore, ['role']),
        ...mapState(useLieuxStore, ['categories', 'departements']),
        ...mapWritableState(useLieuxStore, ['lieux'])
    },

    data() {
        return {
            lieuId: this.$route.params.id,
            lieu: "",
            nom: "",
            description: "",
            latitude: "",
            longitude: "",
            categorie_id: "",
            note: "",
            temps: "",
            difficulte: "",
            kilometres: "",
            departement_id: "",
            adresse: "",
            code_postal: "",
            ville: "",
            statut: "",
            statutPrecedent: "",
            commentaire: "",
            validationErrors: ""
        }
    },

    components: { ValidationErrors },

    methods: {
        ...mapActions(useLieuxStore, ['storeLieux']),

        // cette fonction permet de mettre à jour les données locales du composant
        // une fois que l'appel API a récupéré le lieu
        updateLocalData(lieu) {
            this.lieu = lieu
            this.nom = lieu.nom
            this.description = lieu.description
            this.latitude = lieu.latitude
            this.longitude = lieu.longitude
            this.note = lieu.note
            this.categorie_id = lieu.categorie.id
            this.temps = lieu.temps
            this.difficulte = lieu.difficulte
            this.kilometres = lieu.kilometres
            this.departement_id = lieu.departement_id
            this.adresse = lieu.adresse
            this.code_postal = lieu.code_postal
            this.ville = lieu.ville
            this.statut = lieu.statut
            this.statutPrecedent = lieu.statut
            this.commentaire = lieu.commentaire
        },

        saveChanges() {
            // on sauvegarde les changements dans la base de données
            axios.put('/api/lieus/' + this.lieu.id, {
                nom: this.nom,
                description: this.description,
                latitude: this.latitude,
                longitude: this.longitude,
                categorie_id: this.categorie_id,
                note: this.note,
                temps: this.temps,
                difficulte: this.difficulte,
                kilometres: this.kilometres,
                departement_id: this.departement_id,
                adresse: this.adresse,
                code_postal: this.code_postal,
                ville: this.ville,
                // si le statut était "à modifier", on le fait passer automatiquement à "en attente"
                // sinon, on renvoie le statut actuel
                statut: this.statut == "à modifier" ? "en attente" : this.statut,
                commentaire: this.commentaire
            })
                .then(response => this.handleSuccess(response))
                // en cas d'erreur sur la modification de lieu, on affiche les erreurs
                .catch((error) => {
                    this.validationErrors = error.response.data.errors;
                })
        },

        handleSuccess(response) {
            let message = response.data.message

            // on actualise le lieu modifié dans la liste des lieux contenue dans le backOfficeStore
            // cela évite de faire un appel api
            // on repère d'abord l'index du lieu modifié
            let index = this.lieux.findIndex(lieu => lieu.id == this.lieu.id)
            // puis on le remplace par sa nouvelle version
            this.lieux[index] = response.data.data

            // on sauvegarde dans le store
            this.storeLieux(this.lieux)

            // on envoie une notification en cas de changement de statut
            if (this.statutPrecedent !== this.statut && this.statut !== "en attente") {
                this.sendNotification()
            }

            //on redirige sur le message de succès
            this.$router.push('/successmessage/lastpage/' + message)

        },

        sendNotification() {
            let titre = ""
            let message = ""

            switch (this.statut) {

                case ("validé"):
                    titre = `Félicitations ${this.lieu.user.pseudo}, votre lieu ${this.nom} a été validé !`;
                    message = `Après vérification, j'ai décidé de valider votre lieu : ${this.nom}.<br> 
                                <i style="color:#94D1BE" class="mx-auto fa-solid fa-circle-check fa-5x p-2"></i><br>
                                Vous pouvez déjà le retrouver sur la carte ou sur votre onglet "mes lieux postés".<br>
                                Merci pour ce partage, Nice Places est maintenant plus complet grâce à vous !<br>
                                A très bientôt.<br>
                                <span class="text-right">L'administrateur.</span>`
                    break
                case ("à modifier"):
                    titre = `${this.lieu.user.pseudo}, votre lieu ${this.nom} a besoin de modifications pour être validé.`;
                    message = `Après vérification, votre lieu ${this.nom} a besoin des modifications suivantes : <br> 
                                ${this.commentaire}<br>
                                <i style="color:#94D1BE" class="mx-auto fa-solid fa-pen-to-square fa-5x p-2"></i><br>
                                Merci de faire le nécessaire pour qu'il soit validé.<br>
                                A très bientôt.<br>
                                <span class="text-right">L'administrateur.</span>`
                    break
                case ("refusé"):
                    titre = `${this.lieu.user.pseudo}, votre lieu ${this.nom} a été refusé.`;
                    message = `Après vérification, j'ai décidé de refuser votre lieu : ${this.nom}, pour la ou les raison(s) suivante(s) : <br> 
                                ${this.commentaire}<br>
                                <i class="mx-auto fa-solid fa-xmark fa-5x p-2 text-danger"></i><br>
                                Merci de votre compréhension.<br>
                                A très bientôt.<br>
                                <span class="text-right">L'administrateur.</span>`
                    break
            }

            axios.post('/api/notifications', { titre: titre, message: message, user_id: this.lieu.user.id, lieu_id: this.lieu.id },)
                .then(response => console.log(response.data.message))
                .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    },

    created() {

        axios.get("/api/lieus/" + this.lieuId)
            .then(response => {
                this.updateLocalData(response.data.data)
            }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
            })
    },
}
</script>
