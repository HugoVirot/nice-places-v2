<template>
    <div class="p-5">
        <i class="greenIcon fa-solid fa-3x fa-wrench"></i>
        <h1 class="mt-2">Back-office</h1>
    </div>

    <!-- évènements : lieux / avis / images à valider -->
    <div class="container">
        <div class="row">
            <!-- images à valider-->
            <div v-if="countNewImages > 0" @click="showImages = !showImages"
                class="mx-auto text-danger border border-4 rounded border-danger col-md-4 offset-md-1 py-5">
                <i class="fa-5x fa-solid fa-images mb-3"></i>
                <p class="fs-3"><span class="fs-1">{{ countNewImages }}</span> image(s) à valider</p>
            </div>
            <!-- images à jour-->
            <div v-else @click="showImages = !showImages"
                class="mx-auto border border-4 rounded border-secondary col-md-4 offset-md-1 py-5">
                <i class="blueElement fa-5x fa-solid fa-images mb-3"></i>
                <p class="fs-3"><i class="fa-2x fa-solid fa-check greenIcon mb-3"></i>images à jour</p>
            </div>

            <!-- lieux à valider-->
            <div v-if="countNewLieux > 0" @click="showLieux = !showLieux"
                class="mx-auto text-danger border border-4 rounded border-danger col-md-4 offset-md-1 py-5">
                <i class="fa-5x fa-solid fa-images mb-3"></i>
                <p class="fs-3"><span class="fs-1">{{ countNewLieux }}</span> lieu(x) à valider</p>
            </div>
            <!-- lieux à jour-->
            <div v-else @click="showLieux = !showLieux"
                class="mx-auto border border-4 rounded border-secondary col-md-4 offset-md-1 py-5">
                <i class="blueElement fa-5x fa-solid fa-location-dot mb-3"></i>
                <p class="fs-3"><i class="fa-2x fa-solid fa-check greenIcon mb-3"></i>lieux à jour</p>
            </div>

            <!-- avis à valider-->
            <div v-if="countNewAvis > 0" @click="showAvis = !showAvis"
                class="mx-auto text-danger border border-4 rounded border-danger col-md-4 offset-md-1 py-5">
                <i class="fa-5x fa-solid fa-comment mb-3"></i>
                <p class="fs-3"><span class="fs-1">{{countNewAvis }}</span> avis à valider</p>
            </div>
            <!-- avis à jour-->
            <div v-else @click="showAvis = !showAvis"
                class="mx-auto border border-4 rounded border-secondary col-md-4 offset-md-1 py-5">
                <i class="blueElement fa-5x fa-solid fa-comment mb-3"></i>
                <p class="fs-3"><i class="fa-2x fa-solid fa-check greenIcon mb-3"></i>avis à jour</p>
            </div>
        </div>
    </div>

    <div class="container my-4" id="entities">
        <div class="row">
            <div @click="showLieux = !showLieux"
                class="col-md-6 col-lg-4 col-xl-2 offset-xl-1 py-5 border border-secondary">
                <i class="greenIcon fa-3x fa-solid fa-map-location-dot"></i>
                <p class="fs-3">Lieux</p>
            </div>
            <div @click="showCategories = !showCategories"
                class="col-md-6 col-lg-4 col-xl-2 py-5 border border-secondary">
                <i class="greenIcon fa-solid fa-3x fa-rectangle-list"></i>
                <p class="fs-3">Catégories</p>
            </div>
            <div @click="showAvis = !showAvis" class="col-md-6 col-lg-4 col-xl-2 py-5 border border-secondary">
                <i class="greenIcon fa-solid fa-3x fa-pen"></i>
                <p class="fs-3">Avis</p>
            </div>
            <div @click="showUsers = !showUsers" class="col-md-6 col-lg-4 col-xl-2 py-5 border border-secondary">
                <i class="greenIcon fa-solid fa-3x fa-user"></i>
                <p class="fs-3">Utilisateurs</p>
            </div>
            <div @click="showImages = !showImages" class="col-md-6 col-lg-4 col-xl-2 py-5 border border-secondary">
                <i class="greenIcon fa-3x fa-solid fa-camera"></i>
                <p class="fs-3">Images</p>
            </div>
        </div>
    </div>

    <!-- modal de confirmation de suppression -->

    <div class="modal" ref="confirmationModal" tabindex="-1" id="confirmationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Demande de confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Etes-vous sûr de vouloir supprimer cet élément : {{ elementToDelete }} (id : {{
                    idOfElementToDelete }}) ?</p>
                    <p v-if="elementToDelete == 'category'" class="text-danger">Attention : tous les lieux associés à
                        cette catégorie seront supprimés.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal"
                        @click="handleDeleting()">Oui</button>
                    <button type="button" class="btn btn-danger">Non</button>
                </div>
            </div>
        </div>
    </div>


    <section v-if="showLieux" class="container-fluid">

        <h2 class="mb-2">Liste des lieux</h2>

        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">statut</th>
                    <th scope="col">nom</th>
                    <th scope="col">modifier</th>
                    <th scope="col">supprimer</th>
                    <th scope="col">description</th>
                    <th scope="col">latitude</th>
                    <th scope="col">longitude</th>
                    <th scope="col">note</th>
                    <th scope="col">temps</th>
                    <th scope="col">difficulté</th>
                    <th scope="col">kilomètres</th>
                    <th scope="col">adresse</th>
                    <th scope="col">code postal</th>
                    <th scope="col">ville</th>
                    <th scope="col">commentaire</th>
                    <th scope="col">posté par</th>
                    <th scope="col">ajouté le</th>
                    <th scope="col">modifié le</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="lieu in lieux">
                    <th scope="row">{{ lieu.id }}</th>
                    <td v-if="lieu.statut == 'validé'" class="bg-success text-white">validé</td>
                    <td v-else-if="lieu.statut == 'en attente'" class="bg-info text-white">en attente de validation
                    </td>
                    <td v-else-if="lieu.statut == 'à modifier'" class="bg-warning text-white">à modifier pour être
                        validé
                    </td>
                    <td v-else class="bg-danger text-white">refusé</td>
                    <td>{{ lieu.nom }}</td>
                    <td>
                        <router-link :to="`/modifierlieu/${lieu.id}`"><i class="fa-solid fa-pen-to-square fa-2x greenIcon"></i>
                        </router-link>
                    </td>
                    <td><i class="fa-solid fa-eraser fa-2x text-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal"
                            @click="elementToDelete = 'lieu'; idOfElementToDelete = lieu.id"></i></td>
                    <td>{{ lieu.description.substring(0, 100) }}</td>
                    <td>{{ lieu.latitude }}</td>
                    <td>{{ lieu.longitude }}</td>
                    <td>{{ lieu.note }}</td>
                    <td>{{ lieu.temps }}</td>
                    <td>{{ lieu.difficulte }}</td>
                    <td>{{ lieu.kilometres }}</td>
                    <td>{{ lieu.adresse }}</td>
                    <td>{{ lieu.code_postal }}</td>
                    <td>{{ lieu.ville }}</td>
                    <td>{{ lieu.commentaire ? lieu.commentaire : "aucun" }}</td>
                    <td>{{ lieu.user ? lieu.user.pseudo : 'utilisateur supprimé'}}</td>
                    <td>{{ moment(lieu.created_at).format("ddd DD MMM YYYY [à] HH:mm") }}</td>
                    <td> {{ lieu.created_at == lieu.updated_at ? "jamais modifié" :
                    moment(avis.updated_at).format("ddd DD MMM YYYY [à] HH:mm")
                    
                    }}</td>
                </tr>

            </tbody>
        </table>
    </section>

    <section v-if="showCategories" class="container-fluid p-3 p-lg-5">

        <h2 class="mb-3">Liste des catégories</h2>

        <div class="container">
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">nom</th>
                        <th scope="col">icône</th>
                        <th scope="col">ajoutée le</th>
                        <th scope="col">modifiée le</th>
                        <th scope="col">modifier</th>
                        <th scope="col">supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories">
                        <th scope="row">{{ category.id }}</th>
                        <td>{{ category.nom }}</td>
                        <td><span class="fa-2x iconWithShadow me-2" v-html="category.icone"
                                :style="{color: category.couleur}"></span></td>
                        <td>{{ moment(category.created_at).format("ddd DD MMM YYYY [à] HH:mm") }}</td>
                        <td> {{ category.created_at == category.updated_at ? "jamais modifiée" :
                        moment(category.updated_at).format("ddd DD MMM YYYY [à] HH:mm")
                        
                        }}</td>
                        <td>
                            <router-link :to="`/modifiercategorie/${category.id}`">
                                <i class="fa-solid fa-pen-to-square fa-2x greenIcon"></i>
                            </router-link>
                        </td>
                        <td><i class="fa-solid fa-eraser fa-2x text-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                @click="elementToDelete = 'category'; idOfElementToDelete = categorie.id"></i></td>
                    </tr>

                </tbody>
            </table>
        </div>

        <h2 class="mt-5">Ajouter une catégorie</h2>

        <div class="row justify-content-center p-2 p-lg-5">

            <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Entrez ici les informations</div>

                    <div class="card-body">

                        <form @submit.prevent="storeCategory">

                            <div class="form-group row m-2">
                                <label for="nom" class="col-md-4 col-form-label text-md-right">nom</label>

                                <div class="col-md-6">
                                    <input v-model="nom" id="nom" type="text" class="form-control" name="nom" required
                                        autocomplete="nom">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="icone" class="col-md-4 col-form-label text-md-right">icône</label>

                                <div class="col-md-6">
                                    <input v-model="icone" id="icone" type="text" class="form-control" name="icone"
                                        required autocomplete="icone">
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="couleur" class="col-md-4 col-form-label text-md-right">couleur</label>

                                <div class="col-md-6">
                                    <input v-model="couleur" id="couleur" type="text" class="form-control"
                                        name="couleur" required autocomplete="couleur">
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn btn-lg rounded-pill text-light greenButton">
                                        Valider
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section v-if="showAvis" class="container-fluid p-3 p-lg-5">

        <h2 class="mb-2">Liste des avis</h2>

        <div class="container">
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">statut</th>
                        <th scope="col">utilisateur</th>
                        <th scope="col">lieu</th>
                        <th scope="col">note</th>
                        <th scope="col">commentaire</th>
                        <th scope="col">date de création</th>
                        <th scope="col">date de modification</th>
                        <th scope="col">modifier</th>
                        <th scope="col">supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="avis in avis">
                        <th scope="row">{{ avis.id }}</th>
                        <td v-if="avis.statut == 'validé'" class="bg-success text-white">validé</td>
                        <td v-else-if="avis.statut == 'en attente'" class="bg-info text-white">en attente de
                            validation
                        </td>
                        <td v-else class="bg-danger text-white">refusé</td>
                        <td>{{ avis.user ? avis.user.pseudo : "utilisateur supprimé" }}</td>
                        <td>{{ avis.lieu.nom }}</td>
                        <td>{{ avis.note }}</td>
                        <td v-if="avis.commentaire">{{ avis.commentaire.substring(0, 60) }}</td>
                        <td v-else>aucun</td>
                        <td>{{ moment(avis.created_at).format("ddd DD MMM YYYY [à] HH:mm") }}</td>
                        <td> {{ avis.created_at == avis.updated_at ? "jamais modifié" :
                        moment(avis.updated_at).format("ddd DD MMM YYYY [à] HH:mm")
                        
                        }}</td>
                        <td>
                            <router-link :to="`/modifieravis/${avis.id}`"><i class="fa-solid fa-pen-to-square fa-2x greenIcon"></i>
                            </router-link>
                        </td>
                        <td><i class="fa-solid fa-eraser fa-2x text-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                @click="elementToDelete = 'avis'; idOfElementToDelete = avis.id"></i></td>
                    </tr>

                </tbody>
            </table>
        </div>

    </section>

    <section v-if="showUsers" class="container-fluid p-3 p-lg-5">

        <h2 class="mb-2">Liste des utilisateurs</h2>

        <div class="container">
            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">pseudo</th>
                        <th scope="col">email</th>
                        <th scope="col">role</th>
                        <th scope="col">inscrit le</th>
                        <th scope="col">modifié le</th>
                        <th scope="col">supprimer le compte</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <th scope="row">{{ user.id }}</th>
                        <td>{{ user.pseudo }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.role.role }}</td>
                        <td>{{ moment(user.created_at).format("ddd DD MMM YYYY [à] HH:mm") }}</td>
                        <td> {{ user.created_at == user.updated_at ? "jamais modifié" :
                        moment(user.updated_at).format("ddd DD MMM YYYY [à] HH:mm")
                        
                        }}</td>
                        <td><i class="fa-solid fa-eraser fa-2x text-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                @click="elementToDelete = 'user'; idOfElementToDelete = user.id"></i></td>
                    </tr>

                </tbody>
            </table>
        </div>

    </section>

    <section v-if="showImages" class="container-fluid  p-3 p-lg-5">
        <h2 class="mb-2">Liste des images</h2>

        <table class="table table-striped table-responsive">
            <thead>
                <tr>
                    <th scope="col">aperçu</th>
                    <th scope="col">statut</th>
                    <th scope="col">id</th>
                    <th scope="col">nom</th>
                    <th scope="col">postée par</th>
                    <th scope="col">mise en avant</th>
                    <th scope="col">longueur</th>
                    <th scope="col">largeur</th>
                    <th scope="col">taille</th>
                    <th scope="col">ajoutée le</th>
                    <th scope="col">modifier</th>
                    <th scope="col">supprimer</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="image in images">
                    <td>
                        <button class="m-3" data-bs-toggle="modal" :data-bs-target="`.imageZoom${image.id}`"
                            style="border: none; outline:none">
                            <img v-if="image.lieu_id !== null" class="w-75" :src="`/images/${image.nom}`"
                                :alt="`${image.nom}`">
                            <img v-else class="w-75" :src="`/images/categorie${image.id}.jpg`" :alt="`${image.nom}`">
                        </button>
                    </td>
                    <td v-if="image.statut == 'validée'" class="bg-success text-white">validée</td>
                    <td v-else-if="image.statut == 'en attente'" class="bg-info">en attente de validation
                    </td>
                    <td v-else class="bg-danger text-white">refusée</td>
                    <td>{{ image.id }}</td>
                    <td>{{ image.nom }}</td>
                    <td>{{ image.user ? image.user.pseudo : 'utilisateur supprimé'}}</td>
                    <td>{{ image.mise_en_avant ? 'oui' : 'non' }}</td>
                    <td>{{ image.longueur }} px</td>
                    <td>{{ image.largeur }} px</td>
                    <td>{{ image.taille }} kb</td>
                    <td>{{ moment(image.created_at).format("ddd DD MMM YYYY [à] HH:mm") }}</td>
                    <td>
                        <router-link :to="`/modifierimage/${image.id}`"><i class="fa-solid fa-pen-to-square fa-2x greenIcon"></i>
                        </router-link>
                    </td>
                    <td><i class="fa-solid fa-eraser fa-2x text-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal"
                            @click="elementToDelete = 'image'; idOfElementToDelete = image.id"></i></td>

                    <!-- modal pour afficher l'image en grand -->
                    <div :class="`modal fade imageZoom${image.id}`" tabindex="-1" role="dialog"
                        aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-body w-100">
                                <img v-if="image.lieu_id" class="w-100" :src="`/images/${image.nom}`"
                                    :alt="`${image.nom}`">
                                <img v-else class="w-100" :src="`/images/categorie${image.id}.jpg`"
                                    :alt="`${image.nom}`">
                            </div>
                        </div>
                    </div>
                </tr>
            </tbody>
        </table>
    </section>

</template>

<script setup>
import moment from 'moment';
import axios from "axios";
import { mapWritableState } from 'pinia';
import { mapActions } from 'pinia';
import { useBackOfficeStore } from '../../stores/backOfficeStore'
import { useLieuxStore } from '../../stores/lieuxStore';
import ValidationErrors from '../utilities/ValidationErrors.vue'

moment.locale('fr');

export default {
    // computed permet de surveiller automatiquement les changements
    // des variables dans le state => automatiquement à jour en cas
    // d'ajout / suppression / modif de lieu et/ou de catégorie

    computed: {
        ...mapWritableState(useBackOfficeStore, ['avis', 'users', 'images']),

        ...mapWritableState(useLieuxStore, [
            'lieux',
            'categories'
        ]),

        // renvoie le nombre d'images avec statut "en attente"
        countNewImages() {
            if (this.images) {
                return this.images.filter(image => image.statut == "en attente").length
            }
        },

        // renvoie le nombre de lieux avec statut "en attente" ou "à modifier"
        countNewLieux() {
            if (this.lieux) {
                return this.lieux.filter(lieu => lieu.statut == "en attente" | lieu.statut == "à modifier").length
            }
        },

        // renvoie le nombre d'images avec statut "en attente"
        countNewAvis() {
            if (this.avis) {
                return this.avis.filter(avi => avi.statut == "en attente").length
            }
        },

    },

    data() {
        return {
            // nom - icône - couleur : pour création catégorie
            nom: "",
            icone: "",
            couleur: "",
            showConfirm: false,
            showUsers: false,
            showLieux: false,
            showCategories: false,
            showAvis: false,
            showImages: false,
            elementToDelete: '',
            idOfElementToDelete: '',
            validationErrors: ''
        }
    },

    components: { ValidationErrors },

    methods: {

        ...mapActions(useBackOfficeStore, ['storeAvis', 'storeUsers', 'storeImages']),

        ...mapActions(useLieuxStore, ['storeLieux', 'storeCategories']),

        showModal() {
            this.showConfirm = true;
            this.$nextTick(() => {
                this.$refs.confirmationModal.modal('show');
            });
        },

        handleDeleting() {
            switch (this.elementToDelete) {
                case ('user'):
                    this.deleteUser(this.idOfElementToDelete)
                    break
                case ('lieu'):
                    this.deleteLieu(this.idOfElementToDelete)
                    break
                case ('avis'):
                    this.deleteReview(this.idOfElementToDelete)
                    break
                case ('image'):
                    this.deleteImage(this.idOfElementToDelete)
                    break
                case ('categorie'):
                    this.deleteCategory(this.idOfElementToDelete)
                    break
            }
        },

        deleteReview(id) {

            axios.delete("/api/avis/" + id)

                .then(response => {

                    let message = response.data.message

                    // une fois l'avis supprimé, on le retire des avis du store
                    // cela permet d'éviter un appel api qui récupérerait tous les avis alors qu'un seul a changé
                    let index = this.avis.findIndex(avi => avi.id == id)
                    this.avis.splice(index, 1)

                    // on sauvegarde la nouvelle liste dans le store
                    this.storeAvis(this.avis)

                    // on redirige vers l'accueil en affichant le message de succès
                    this.$router.push('/successmessage/backoffice/' + message)

                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        deleteLieu(id) {

            axios.delete("/api/places/" + id)
                .then(response => {

                    let message = response.data.message

                    // même principe que pour les avis ci-dessus
                    let index = this.lieux.findIndex(lieu => lieu.id == id)
                    this.lieux.splice(index, 1)

                    // on sauvegarde la nouvelle liste dans le store
                    this.storeLieux(this.lieux)

                    // on redirige vers l'accueil en affichant le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)

                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        deleteCategory(id) {

            axios.delete("/api/categories/" + id)

                .then(response => {

                    let message = response.data.message

                    // même principe que pour les avis ci-dessus
                    let index = this.categories.findIndex(category=> categorie.id == id)
                    this.categories.splice(index, 1)

                    // on sauvegarde la nouvelle liste dans le store
                    this.storeCategories(this.categories)

                    // on redirige vers l'accueil en affichant le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)

                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        storeCategory() {
            axios.post('/api/categories', { nom: this.nom, icone: this.icone, couleur: this.couleur })
                .then(response => {
                    let message = response.data.message

                    // on ajoute la nouvelle catégorie à la liste, qu'on sauvegarde dans le store
                    // cela évite de faire un appel API
                    this.categories.push(response.data.data)
                    this.storeCategories(this.categories)

                    // on redirige vers le back-office en affichant le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)

                }).catch((error) => {
                    this.validationErrors = error.response.data.errors;
                })
        },

        deleteUser(id) {

            axios.delete("/api/users/" + id)

                .then(response => {

                    let message = response.data.message

                    // même principe que pour les avis ci-dessus
                    let index = this.users.findIndex(user => user.id == id)
                    this.users.splice(index, 1)

                    // on sauvegarde la nouvelle liste dans le store
                    this.storeUsers(this.users)

                    // on redirige vers l'accueil en affichant le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)

                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        deleteImage(id) {

            axios.delete("/api/images/" + id)

                .then(response => {

                    let message = response.data.message

                    // même principe que pour les avis ci-dessus
                    let index = this.images.findIndex(image => image.id == id)
                    this.images.splice(index, 1)

                    // on sauvegarde la nouvelle liste dans le store
                    this.storeImages(this.images)

                    // on redirige vers l'accueil en affichant le message de succès
                    this.$router.push('/successmessagebackoffice/' + message)

                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        getLieux() { // on récupère tous les lieux même les non validés. Ils remplacent la liste de lieux "normaux" (uniquement ceux validés).
            axios.get("/api/getallplaces")
                .then(response => {
                    this.storeLieux(response.data.data)
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        getAvis() {
            axios.get("/api/avis")
                .then(response => {
                    this.storeAvis(response.data.data)
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        getUsers() {
            axios.get("/api/users")
                .then(response => {
                    this.storeUsers(response.data.data)
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        getImages() {
            axios.get("/api/images")
                .then(response => {
                    this.storeImages(response.data.data)
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    },

    created() {
        // on initialise moment pour afficher la date en français
        this.moment = moment

        // on récupère lieux / avis / images / users (catégories déjà récupérées sur App.vue)
        this.getLieux()
        this.getAvis()
        this.getImages()
        this.getUsers()
    }
}
</script>

<style scoped>
.card-header,
#entities p {
    color: #1c6e8c
}

@media screen and (max-width: 1200px) {
    img {
        width: 30vw !important
    }
}
</style>