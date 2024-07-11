<template>
    <div v-if="!loading">

        <div class="pt-5 pb-2">
            <!-- <i class="greenIcon mx-auto fa-3x fa-solid fa-map-location-dot"></i> -->
            <p class="fs-5">catégorie :<span class="greenIcon p-2 ms-2 fa-2x" :style="{ color: lieu.categorie.couleur }"
                    v-html="lieu.categorie.icone"></span>{{
                        lieu.categorie.nom
                    }}</p>
            <h1 class="mt-2">{{ lieu.nom }}</h1>

            <div class="container">

                <div class="row fs-4 mt-4">

                    <div class="col-md-2">
                        <p><i class="yellowStar fa-solid fa-star ms-2 me-1"></i>
                            {{ lieu.note }}</p>
                    </div>

                    <div class="col-md-6" v-if="lieu.user">
                        <p><i class="greenIcon fa-solid fa-user ms-2 me-3"></i><span class="fs-5">Posté par {{
                            lieu.user.pseudo
                        }}</span></p>
                    </div>

                    <div v-if="userLoggedIn" class="col-md-4">
                        <i v-if="isInFavorites" @click="removeToFavorites" class="greenIcon fa-solid fa-heart fa-2x"></i>
                        <i v-else @click="addToFavorites" class="greenIcon fa-regular fa-heart fa-2x"></i>
                    </div>

                    <div v-else class="col-md-4">
                        <p><i class="greenIcon fa-solid fa-heart me-2"></i>
                            <span id="texteFavoris">connectez-vous pour ajouter aux favoris</span>
                        </p>
                    </div>

                </div>

            </div>

        </div>

        <img class="singlePicture" v-if="lieu.images.length == 0" src="/images/defaultpicture.jpg" alt="image par défaut">

        <img class="singlePicture" v-else-if="lieu.images.length == 1" :src="`/images/${lieu.images[0].nom}`"
            alt="image du lieu">

        <div v-else id="carouselLieu" class="carousel slide carousel-fade container" data-bs-ride="carousel">

            <div class="carousel-inner">

                <div v-for="(image, index) in lieu.images"
                    :class="['carousel-item', { active: image.mise_en_avant == true }]">
                    <img :src="`/images/${image.nom}`" class="d-block w-100" :alt="image.nom">
                </div>

                <div class="carousel-indicators">
                    <button v-for="(image, index) in lieu.images" type="button" data-bs-target="#carouselLieu"
                        :data-bs-slide-to="index" :class="{ active: image.mise_en_avant == true }" aria-current="true"
                        :aria-label="`Slide ${index + 1}`">
                    </button>
                </div>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselLieu" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#carouselLieu" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div id="detailsLieu" class="container p-4">

            <p class="m-3">{{ lieu.description }}</p>

            <div class="row px-5 py-4 mt-4 border border-secondary">
                <div class="col-md-6">
                    <p> <i class="fa-2x fa-solid fa-road me-3"></i>
                        {{ lieu.adresse }}</p>
                    <p> <i class="fa-2x fa-solid fa-location-dot me-3"></i>
                        {{ lieu.code_postal + " " + lieu.ville.toUpperCase() }}</p>
                    <p> <i class="fa-2x fa-solid fa-hourglass-half me-3"></i>
                        {{ lieu.temps }} heure(s)
                    </p>
                    <p><i class="fa-2x fa-solid fa-gauge-simple-high me-3"></i>
                        {{ lieu.difficulte }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><i class="fa-2x fa-solid fa-earth-europe me-3"></i>
                        latitude : {{ lieu.latitude }}
                    </p>
                    <p><i class="fa-2x fa-solid fa-earth-europe me-3"></i>
                        longitude : {{ lieu.longitude }}
                    </p>
                    <p><i class="fa-2x fa-solid fa-star me-3"></i>
                        {{ lieu.note }} / 10
                    </p>
                    <p v-if="lieu.kilometres"> <i class="fa-2x fa-solid fa-shoe-prints me-3"></i>
                        {{ lieu.kilometres }} kilomètres (en moyenne)</p>
                    <p>
                        <router-link :to="`/uploadimages/${lieu.id}`"><button
                                class="btn btn-lg greenButton rounded-pill">proposer
                                des images</button></router-link>
                    </p>
                </div>
            </div>
        </div>

        <Map v-if="lieu" :lieuSeul="lieu" />

        <h2 class="m-5">Avis sur ce lieu</h2>

        <div v-if="lieu.avis.length > 0">
            <div v-for="(avis, index) in lieu.avis">

                <div class="container p-4 mb-3 border border-secondary" v-if="index + 1 <= 5">

                    <div class="row">
                        <div class="col-md-4 p-3 p-md-0">
                            <i class="yellowStar fa-solid fa-star fa-2x ms-2 me-1"></i>
                            {{ avis.note }} / 10
                        </div>
                        <div class="col-md-4 p-3 p-md-0">
                            <i class="fa-solid greenIcon fa-user fa-2x ms-2 me-3"></i>Posté par {{ avis.user.pseudo }}
                        </div>
                        <div class="col-md-4 p-3 p-md-0">
                            <i class="fa-solid blueElement fa-calendar-days fa-2x ms-2 me-3"></i>
                            <p class="text-dark">{{ moment(avis.created_at).format("ddd DD MMM YYYY [à] HH:mm") }}</p>
                        </div>
                    </div>

                    <div v-if="avis.commentaire">
                        <!-- si - de 500 caractères  -->
                        <p v-if="avis.commentaire && avis.commentaire.length <= 500">{{ avis.commentaire }}</p>

                        <!-- si + de 500 caractères  -->
                        <p v-else>
                            <!-- si commentaire limité à 500 => bouton "lire plus" pour afficher  -->
                        <div v-if="!showFullComment">
                            {{ avis.commentaire.substring(0, 500) + "..." }}
                            <span class="blueElement" @click="showFullComment = !showFullComment">lire plus</span>
                        </div>

                        <!-- si commentaire entièrement affiché => bouton "lire moins" pour replier  -->
                        <div v-else>
                            {{ avis.commentaire }}
                            <span class="blueElement" @click="showFullComment = !showFullComment">lire moins</span>
                        </div>
                        </p>
                    </div>

                    <div v-else>
                        <p>aucun commentaire</p>
                    </div>
                </div>

                <div v-else>
                    <!-- si avis n°6 ou + : on l'affiche (ainsi que les autres de numéro > 6) au clic sur le bouton -->
                    <button v-if="index + 1 == 6 && !showAllReviews" class="btn btn-lg greenButton rounded-pill mb-2"
                        @click="showAllReviews = !showAllReviews">Afficher tous les avis</button>

                    <button v-if="index + 1 == 6 && showAllReviews" class="btn btn-lg greenButton rounded-pill mb-2"
                        @click="showAllReviews = !showAllReviews">Masquer</button>

                    <div v-if="showAllReviews">

                        <div class="container p-4 mb-3 border border-secondary">

                            <div class="row">
                                <div class="col-md-4 p-3 p-md-0">
                                    <i class="yellowStar fa-solid fa-star fa-2x ms-2 me-1"></i>
                                    {{ avis.note }} / 10
                                </div>
                                <div class="col-md-4 p-3 p-md-0">
                                    <i class="fa-solid fa-user fa-2x ms-2 me-3"></i>Posté par {{ avis.user.pseudo }}
                                </div>
                                <div class="col-md-4 p-3 p-md-0">
                                    <i class="fa-solid fa-calendar-days fa-2x ms-2 me-3"></i>
                                    <p class="text-dark">{{ moment(avis.created_at).format("ddd DD MMM YYYY [à]HH:mm")
                                    }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="avis.commentaire">

                                <!-- si - de 500 caractères  -->
                                <p v-if="avis.commentaire && avis.commentaire.length <= 500">{{ avis.commentaire }}</p>

                                <!-- si + de 500 caractères  -->
                                <p v-else>
                                    <!-- si commentaire limité à 500 => bouton "lire plus" pour afficher  -->
                                <div v-if="!showFullComment">
                                    {{ avis.commentaire.substring(0, 500) + "..." }}
                                    <span class="blueElement fs-5" @click="showFullComment = !showFullComment">lire
                                        plus</span>
                                </div>

                                <!-- si commentaire entièrement affiché => bouton "lire moins" pour replier  -->
                                <div v-else>
                                    {{ avis.commentaire }}
                                    <span class="blueElement fs-5" @click="showFullComment = !showFullComment">lire
                                        moins</span>
                                </div>

                                </p>

                            </div>


                            <div v-else>
                                <p>aucun commentaire</p>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <p>Aucun avis posté. Partagez le vôtre !</p>
        </div>

        <PosterAvis :lieu_id="lieu.id" />
    </div>
</template>

<script>
import axios from 'axios'
import Map from '../utilities/Map.vue'
import moment from 'moment';
moment.locale('fr');
import PosterAvis from '../utilities/PosterAvis.vue';
import { useUserStore } from "../../stores/userStore"
import { mapState } from 'pinia';
import { mapActions } from 'pinia';

export default {

    computed: {
        ...mapState(useUserStore, ['id', 'userLoggedIn', 'favoris']),

        isInFavorites() {
            // on retourne true si le lieu fait partie des favoris de l'utilisateur
            // la fonction some permet de vérifier cela (on cherche un favori avec le nom du lieu)
            return this.favoris ? this.favoris.some(favori => favori.nom === this.lieu.nom) : null
        }
    },

    data() {
        return {
            lieuId: this.$route.params.id,
            lieu: null,
            showAllReviews: false,
            showFullComment: false,
            loading: true
        };
    },

    components: {
        Map, PosterAvis
    },

    methods: {
        ...mapActions(useUserStore, ['storeFavoris']),

        addToFavorites() {
            axios.post('/api/favoris', { lieu_id: this.lieuId, user_id: this.id })

                .then(response => {
                    let message = response.data.message

                    axios.get('/api/favoris/' + this.id)

                        .then(response => {
                            this.storeFavoris(response.data.data)
                            this.$router.push('/successmessagelastpage/' + message)
                        }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                        })
                }).catch(() => {
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        removeToFavorites() {
            axios.delete('/api/favoris/' + this.id + '/' + this.lieuId)

                .then(response => {
                    let message = response.data.message

                    axios.get('/api/favoris/' + this.id)

                        .then(response => {
                            this.storeFavoris(response.data.data)
                            this.$router.push('/successmessagelastpage/' + message)
                        }).catch(() => {
                            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                        })
                }).catch(() => {
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    },

    created() {
        // on initialise moment pour formater les dates
        this.moment = moment

        // on récupère les infos du lieu (voir pour les passer en props)
        axios.get("/api/lieus/" + this.lieuId)
            .then((response) => {
                this.lieu = response.data.data
                this.loading = false
            }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
            })
    }
}
</script>

<style scoped>
.singlePicture {
    width: 90vw
}

#texteFavoris {
    font-size: small;
}

#detailsLieu p {
    display: flex;
    align-items: center
}

#detailsLieu i {
    color: #1c6e8c
}
</style>