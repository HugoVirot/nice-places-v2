<template>

    <div class="p-3" v-if="avis">
        <i class="fa-3x fa-solid fa-pen-to-square"></i>
        <h1 class="mt-2">Modifier avis n°{{ avis.id }}</h1>
        <h2 v-if="avis.user">Posté par {{ avis.user.pseudo }}</h2>
        <h2 v-else>Posté par : utilisateur supprimé</h2>
        <div>
            <div v-if="statut == 'validé'" class="mx-auto bg-success text-white w-25">validé</div>
            <div v-else-if="statut == 'en attente'" class="mx-auto bg-info w-25">en attente de validation
            </div>
            <div v-else class="mx-auto bg-danger w-25">refusé
            </div>
        </div>
    </div>

    <div class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div v-if="avis !== '' && role == 'admin'" class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Entrez les nouvelles informations</div>

                    <div class="card-body">

                        <form @submit.prevent="saveChanges">

                            <div class="form-group row m-2">
                                <label for="statut" class="col-md-4 col-form-label text-md-right">modifier le
                                    statut</label>

                                <div class="col-md-6">
                                    <select required v-model="statut" class="form-select" aria-label="statut">
                                        <option value="validé">validé</option>
                                        <option value="en attente">en attente</option>
                                        <option value="refusé">refusé</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="statut == 'refusé'" class="form-group row m-2">
                                <label for="raisonRefus" class="col-md-4 col-form-label text-md-right">raison du
                                    refus</label>

                                <div class="col-md-6">
                                    <input v-model="raisonRefus" id="raisonRefus" type="text" class="form-control"
                                        name="raisonRefus" required autocomplete="raisonRefus" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="note" class="col-md-4 col-form-label text-md-right">note</label>

                                <div class="col-md-6">
                                    <input v-model="note" id="note" type="number" min="1" max="10" class="form-control"
                                        name="note" required autocomplete="note" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="commentaire"
                                    class="col-md-4 col-form-label text-md-right">commentaire</label>

                                <div class="col-md-6">
                                    <textarea v-model="commentaire" id="commentaire" class="form-control"
                                        name="commentaire" autocomplete="commentaire">
                                        commentaire (facultatif)</textarea>
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit"
                                        class="greenButton btn btn-lg rounded-pill text-light btn-info">
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
import { mapActions, mapState } from 'pinia';
import { useBackOfficeStore } from '../../stores/backOfficeStore';

export default {

    computed: {
        ...mapState(useBackOfficeStore, { listeAvis: 'avis' }),
        ...mapState(useUserStore, ['role'])
    },

    data() {
        return {
            avis: "",
            statut: "",
            statutPrecedent: "",
            note: "",
            commentaire: "",
            validationErrors: "",
            raisonRefus: ""
        }
    },

    components: { ValidationErrors },

    methods: {
        ...mapActions(useBackOfficeStore, ['storeAvis']),

        // cette fonction permet de mettre à jour les données locales du composant
        // une fois que l'appel API a récupéré l'avis
        updateLocalData(avis) {
            this.avis = avis
            this.statut = avis.statut
            this.statutPrecedent = avis.statut
            this.note = avis.note
            this.commentaire = avis.commentaire
        },

        saveChanges() {

            axios.put('/api/avis/' + this.avis.id, {
                statut: this.statut,
                note: this.note,
                commentaire: this.commentaire,
            })
                .then((response) => {
                    let message = response.data.message

                    // on actualise l'avis modifié dans liste avis du backOfficeStore (évite de faire un appel api)
                    // on repère d'abord l'index de l'avis modifié
                    let index = this.listeAvis.findIndex(avis => avis.id == this.avis.id)

                    // // puis on le remplace par sa nouvelle version
                    this.listeAvis[index] = response.data.data

                    // // on sauvegarde dans le store
                    this.storeAvis(this.listeAvis)

                    // on envoie une notification en cas de changement de statut
                    if (this.statutPrecedent !== this.statut && this.statut !== "en attente") {
                        this.sendNotification()
                    }

                    // enfin on redirige sur le back-office avec le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)
                })
                .catch((error) => {
                    this.validationErrors = error.response.data.errors;
                })
        },

        sendNotification() {
            let titre = ""
            let message = ""

            switch (this.statut) {

                case ("validé"):
                    titre = `Félicitations ${this.avis.user.pseudo}, votre avis a été validé !`;
                    message = `<p>Bonjour ${this.avis.user.pseudo}.</p>
                    Après vérification, j'ai décidé de valider votre avis : <br> 
                    <p>Lieu : ${this.avis.lieu.nom} </p>
                    <p>Note : ${this.note} </p>
                    <p>Commentaire : ${this.commentaire ? this.commentaire.substring(0, 100) + '...' : 'aucun'}</p>
                    <i style="color:#94D1BE" class="mx-auto fa-solid fa-circle-check fa-5x p-2"></i><br>
                    <p>Merci pour ce partage !<br>
                    A très bientôt.</p><br>
                    <p class="text-end">L'administrateur.</p>`
                    break

                case ("refusé"):
                    titre = `${this.avis.user.pseudo}, votre avis a été refusé.`;
                    message = `<p>Bonjour ${this.avis.user.pseudo}.</p>
                    Après vérification, j'ai décidé de refuser votre avis : <br> 
                    <p>Lieu : ${this.avis.lieu.nom} </p>
                    <p>Note : ${this.note} </p>
                    <p>Commentaire : ${this.commentaire ? this.commentaire.substring(0, 100) + '...' : 'aucun'}</p>
                    <i class="mx-auto fa-solid fa-xmark fa-5x p-2 text-danger"></i><br>
                    <p>Il a été refusé pour la ou les raison(s) suivante(s) : ${this.raisonRefus}<p>
                    <p>Merci de votre compréhension.<br>
                    A très bientôt.</p><br>
                    <p class="text-end">L'administrateur.</p>`
                    break
            }

            axios.post('/api/notifications', { titre: titre, message: message, user_id: this.avis.user.id, lieu_id: this.avis.lieu.id },)
                .then(response => console.log(response.data.message))
                .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    },

    created() {

        axios.get("/api/avis/" + this.$route.params.id)
            .then(response => {
                this.updateLocalData(response.data.data)
            }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
            })

    },
}
</script>


<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/sunset.jpg);
    background-position: center;
    background-size: cover;
}
</style>