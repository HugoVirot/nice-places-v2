<template>
    <div class="p-3">
        <i class="fa-3x fa-solid fa-pen-to-square"></i>
        <h1 class="mt-2">Modifier l'image {{ image.nom }}</h1>
        <div v-if="image">
            <div v-if="statut == 'validée'" class="mx-auto bg-success text-white w-25">validée</div>
            <div v-else-if="statut == 'en attente'" class="mx-auto bg-info w-25">en attente de validation
            </div>
            <div v-else class="mx-auto bg-danger w-25">refusée
            </div>
        </div>
    </div>

    <p v-if="image">postée par {{ image.user.pseudo }}</p>

    <img class="w-50" :src="`/images/${image.nom}`" :alt="`${image.nom}`">


    <div class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div v-if="image !== '' && role == 'admin'" class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Entrez les nouvelles informations</div>

                    <div class="card-body">

                        <form @submit.prevent="saveChanges">

                            <div class="form-group row m-2" v-if="imagesNumberForThisPlace > 1">
                                <label for="mise_en_avant" class="col-md-4 col-form-label text-md-right">mettre en
                                    avant</label>

                                <div class="col-md-6">
                                    <select required v-model="mise_en_avant" name="mise_en_avant" class="form-select"
                                        aria-label="mise_en_avant">
                                        <option value="1">Oui</option>
                                        <option value="0">Non</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="role == 'admin'" class="form-group row m-2">
                                <label for="statut" class="col-md-4 col-form-label text-md-right">modifier le
                                    statut</label>

                                <div class="col-md-6">
                                    <select required v-model="statut" class="form-select" aria-label="statut">
                                        <option value="validée">validée</option>
                                        <option value="en attente">en attente</option>
                                        <option value="refusée">refusée</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="statut == 'refusée'" class="form-group row m-2">
                                <label for="raisonsRefus" class="col-md-4 col-form-label text-md-right">raisons du
                                    refus</label>

                                <div class="col-md-6">
                                    <textarea v-model="raisonsRefus" id="raisonsRefus" class="form-control"
                                        name="raisonsRefus" autocomplete="raisonsRefus">
                                        image trop pixellisée / en double / non pertinente / etc</textarea>
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

<script setup>
import axios from 'axios'
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useUserStore } from "../../stores/userStore.js";
import { useBackOfficeStore } from '../../stores/backOfficeStore';
import { mapState, mapWritableState } from 'pinia';
import { mapActions } from 'pinia';

export default {

    computed: {
        ...mapState(useUserStore, ['id', 'pseudo', 'role']),
        ...mapWritableState(useBackOfficeStore, ['images'])
    },

    data() {
        return {
            image: "",
            nom: "",
            mise_en_avant: "",
            statut: "",
            raisonsRefus: "",
            imagesNumberForThisPlace: 1,
            validationErrors: ""
        }
    },

    components: { ValidationErrors },

    methods: {
        ...mapActions(useBackOfficeStore, ['storeImages']),

        // cette fonction permet de mettre à jour les données locales du composant
        // une fois que l'appel API a récupéré l'image
        updateLocalData(image) {
            this.image = image
            this.nom = image.nom
            this.mise_en_avant = image.mise_en_avant
            this.statut = image.statut

            if (this.image.lieu_id) {
                axios.get("/api/places/getimagesnumberbyplace/" + this.image.lieu_id)
                    .then(response => {
                        this.imagesNumberForThisPlace = response.data
                    }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                        alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                    })
            }
        },

        // on sauvegarde les changements en bdd via un appel api
        saveChanges() {
            axios.put('/api/images/' + this.image.id, {
                mise_en_avant: this.mise_en_avant,
                statut: this.statut
            })
                .then((response) => {

                    let message = response.data.message
                    let newImage = response.data.data

                    if (this.statut == "validée") { // notification validée => message de succès
                        this.createSuccessNotification()
                        // on actualise la liste des images en remplaçant l'ancienne version par la nouvelle 
                        let index = this.images.findIndex(image => image.id == newImage.id)
                        this.images.splice(index, 1, newImage)
                        // on sauvegarde la nouvelle liste dans le store
                        this.storeImages(this.images)

                    } else if (this.statut == "refusée") { // notification refusée : message de refus avec la raison
                        // else if au lieu de else pour éviter un message de refus si l'image reste en attente
                        this.createRefusalNotification()

                        // on supprime l'image de la bdd (inutile de la conserver plus longtemps)
                        axios.delete("/api/images/" + this.image.id)

                            .then(() => {
                                // une fois l'image  supprimée, on la retire des images du store
                                // cela permet d'éviter un appel api qui récupérerait toutes les images alors qu'une seule a changé
                                let index = this.images.findIndex(image => image.id == this.image.id)
                                this.images.splice(index, 1)

                                // on sauvegarde la nouvelle liste dans le store
                                this.storeImages(this.images)

                                message = "Suppression effectuée et notifiée à l'utilisateur."
                                this.$router.push('/successmessagebackoffice/' + message)
                            }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                                alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                            })
                    }

                    this.$router.push('/successmessagebackoffice/' + message)
                })
                .catch((error) => {
                    this.validationErrors = error.response.data.errors;
                })
        },

        createSuccessNotification() {
            let titre = `Votre image ${this.nom} a bien été validée !`;
            let message = `<p class="text-secondary">Bonjour ${this.image.user.pseudo},<br>
                    Félicitations, votre image a bien été validée !<br>
                    <i style="color: #94D1BE" class="mx-auto my-3 fa-solid fa-circle-check fa-5x"></i><br>
                    Elle est visible dès maintenant sur la page du lieu concerné.<br>
                    N'hésitez pas à en proposer de nouvelles, pour ce lieu ou pour d'autres.<br>
                    Merci et à très bientôt.</p>
                    <p class="text-end">L'administrateur.</p>`

            axios.post('/api/notifications', { titre: titre, message: message, user_id: this.image.user_id, lieu_id: this.image.lieu_id })
                .then(response => console.log(response.data.message))
                .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },
        

        createRefusalNotification() {
            let titre = `Votre image ${this.nom} a été refusée`;
            let message = `<p class="text-secondary">Bonjour ${this.image.user.pseudo},<br>
                Votre image a été refusée pour la (les) raison(s) suivant(es) : <br>
                ${this.raisonsRefus}<br>
                <i style="color: #94D1BE" class="mx-auto my-3 fa-solid fa-xmark fa-5x"></i><br>
                Merci de bien proposer des images correspondant au lieu, bien cadrées et bien nettes.<br>
                N'hésitez pas à en proposer de nouvelles, pour ce lieu ou pour d'autres.<br>
                Merci et à très bientôt.</p>
                <p class="text-end">L'administrateur.</p>`

            axios.post('/api/notifications', { titre: titre, message: message, user_id: this.image.user_id, lieu_id: this.image.lieu_id })
                .then(response => console.log(response.data.message))
                .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },
    },

    created() {

        axios.get("/api/images/" + this.$route.params.id)
            .then(response => {
                this.updateLocalData(response.data.data)
            }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
            })
    },
}
</script>