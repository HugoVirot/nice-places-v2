<template>
    <div class="p-3">
        <i class="fa-3x fa-solid fa-pen-to-square"></i>
        <h1 class="mt-2">Modifier {{ categorie.nom }}</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div v-if="categorie !== '' && role == 'admin'" class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Entrez les nouvelles informations</div>

                    <div class="card-body">

                        <p>aperçu</p>

                        <div class="m-auto border border-secondary mb-3 w-50">
                            <p><span class="fa-3x iconWithShadow me-2" v-if="couleur" v-html="icone"
                                    :style="{color: couleur}"></span>{{ nom }}</p>
                        </div>

                        <form @submit.prevent="saveChanges">

                            <div class="form-group row m-2">
                                <label for="nom" class="col-md-4 col-form-label text-md-right">nom</label>

                                <div class="col-md-7">
                                    <input v-model="nom" id="nom" type="text" class="form-control" name="nom" required
                                        autocomplete="nom" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="icone" class="col-md-4 col-form-label text-md-right">icône
                                    Fontawesome</label>

                                <div class="col-md-7">
                                    <input v-model="icone" id="icone" type="text" class="form-control" name="icone"
                                        required autocomplete="icone">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="couleur" class="col-md-4 col-form-label text-md-right">couleur de l'icône *
                                </label>
                                <div class="col-md-7">
                                    <input v-model="couleur" id="couleur" type="text" class="form-control"
                                        name="couleur" required autocomplete="couleur">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="mx-auto p-2 m-2 text-secondary">
                                    * en toutes lettres, ou code hexadécimal avec le # au début
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn btn-lg greenButton rounded-pill text-light">
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
import { mapState } from 'pinia';

export default {

    computed: {
        ...mapState(useUserStore, ['role']),
        ...mapState(useLieuxStore, ['categories', 'storeCategories'])
    },

    data() {
        return {
            categorie: "",
            nom: "",
            icone: "",
            couleur: "",
            validationErrors: ""
        }
    },

    components: { ValidationErrors },

    methods: {
        // cette fonction permet de mettre à jour les données locales du composant
        // une fois que l'appel API a récupéré la categorie
        updateLocalData(categorie) {
            this.categorie = categorie
            this.nom = categorie.nom
            this.icone = categorie.icone
            this.couleur = categorie.couleur
        },

        saveChanges() {
            axios.put('/api/categories/' + this.categorie.id, {
                nom: this.nom,
                icone: this.icone,
                couleur: this.couleur
            })
                .then((response) => {
                    let message = response.data.message

                    // une fois la catégorie supprimée, on la retire des catégories du store
                    // cela permet d'éviter un appel api
                    let index = this.categories.findIndex(categorie => categorie.id == id)
                    this.categories.splice(index, 1)

                    // on sauvegarde la nouvelle liste dans le store
                    this.storeCategories(this.categories)

                    // on redirige vers l'accueil en affichant le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)

                }).catch((error) => {
                    this.validationErrors = error.response.data.errors;
                })
        }
    },

    created() {

        axios.get("/api/categories/" + this.$route.params.id)
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
    background-image: url(../../../../public/images/forest.jpg);
    background-position: center;
    background-size: cover;
}
</style>