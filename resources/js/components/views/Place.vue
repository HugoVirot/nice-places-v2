<template>
    <LoadingSpinner v-if="loading" />
    <div v-else-if="place">

        <div class="pt-5 pb-3">
            <!-- <i class="greenIcon mx-auto fa-3x fa-solid fa-map-location-dot"></i> -->
            <p class="fs-5">catégorie :<span class="greenIcon p-2 ms-2 fa-2x" :style="{ color: place.category.color }"
                    v-html="place.category.icon"></span>{{
                        place.category.name
                    }}</p>
            <h1 class="mt-2">{{ place.name }}</h1>

            <div class="container">

                <div class="row fs-5 mt-4 align-items-stretch">

                    <div
                        class="col-md-4 d-flex flex-column align-items-center justify-content-center text-center mb-2 mb-md-0">
                        <div class="place-rating d-flex align-items-center justify-content-center gap-2">
                            <p class="place-rating-label mb-0 text-nowrap">moyenne : {{ place.note }}/10</p>
                            <div class="place-rating-stars" role="meter" :aria-valuenow="ratingValue" aria-valuemin="0"
                                aria-valuemax="10" aria-label="Note moyenne sur 10">
                                <i v-for="star in 10" :key="star" class="fa-solid fa-star place-rating-star"
                                    aria-hidden="true"
                                    :style="{ backgroundImage: `linear-gradient(to right, #ffc107 ${ratingFillForStar(star)}%, #1c6e8c ${ratingFillForStar(star)}%)` }"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex flex-column align-items-center justify-content-center text-center"
                        v-if="place.user">
                        <p class="mb-0"><i class="greenIcon fa-solid fa-user ms-2 me-3"></i><span>Posté par {{
                            place.user.name
                                }}</span></p>
                    </div>

                    <div v-if="userStore.userLoggedIn"
                        class="col-md-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <button type="button" class="btn p-0 border-0" :disabled="favoriteLoading"
                            :aria-label="isInFavorites ? 'Retirer des favoris' : 'Ajouter aux favoris'"
                            :title="isInFavorites ? 'Retirer des favoris' : 'Ajouter aux favoris'"
                            @click="toggleFavorite">
                            <i :class="[
                                'fa-2x',
                                isInFavorites ? 'greenIcon fa-solid fa-heart' : 'greenIcon fa-regular fa-heart'
                            ]"></i>
                        </button>
                        <p v-if="favoriteError" class="text-danger mt-2" role="alert">{{ favoriteError }}</p>
                    </div>

                    <div v-else
                        class="col-md-4 d-flex flex-column align-items-center justify-content-center text-center">
                        <p class="mb-0"><i class="greenIcon fa-solid fa-heart me-2"></i>
                            <span id="texteFavoris">connectez-vous pour ajouter aux favoris</span>
                        </p>
                    </div>

                </div>

            </div>

        </div>

        <img class="singlePicture" v-if="place.images.length == 0" :src="'/images/defaultpicture.jpg'"
            alt="image par défaut">

        <img class="singlePicture" v-else-if="place.images.length == 1" :src="`/images/${place.images[0].name}`"
            alt="image du lieu">

        <div v-else id="carouselLieu" class="carousel slide carousel-fade place-gallery" data-bs-ride="carousel">

            <div class="carousel-inner">

                <div v-for="(image, index) in place.images"
                    :class="['carousel-item', { active: image.mise_en_avant == true }]">
                    <img :src="`/images/${image.name}`" class="d-block w-100" :alt="image.name">
                </div>

                <div class="carousel-indicators">
                    <button v-for="(image, index) in place.images" type="button" data-bs-target="#carouselLieu"
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

            <p class="place-description">{{ place.description }}</p>

            <section class="place-details" aria-labelledby="placeDetailsTitle">
                <h2 id="placeDetailsTitle" class="place-details-title">Informations pratiques</h2>
                <div class="place-details-grid">
                    <div class="place-detail place-detail-location">
                        <span class="place-detail-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                        <div class="place-detail-content">
                            <span class="place-detail-label">Adresse</span>
                            <strong>{{ place.adress || "Adresse non renseignée" }}</strong>
                            <span class="place-detail-secondary">{{ place.postcode }} {{ place.city }}</span>
                        </div>
                    </div>

                    <div class="place-detail">
                        <span class="place-detail-icon" aria-hidden="true"><i class="fa-regular fa-clock"></i></span>
                        <div class="place-detail-content">
                            <span class="place-detail-label">Durée</span>
                            <strong>{{ place.time ? `${place.time} heure(s)` : "Non renseignée" }}</strong>
                        </div>
                    </div>

                    <div class="place-detail">
                        <span class="place-detail-icon" aria-hidden="true"><i class="fa-solid fa-gauge-high"></i></span>
                        <div class="place-detail-content">
                            <span class="place-detail-label">Difficulté</span>
                            <strong>{{ place.difficulty || "Non précisée" }}</strong>
                        </div>
                    </div>

                    <div class="place-detail">
                        <span class="place-detail-icon" aria-hidden="true"><i class="fa-solid fa-star"></i></span>
                        <div class="place-detail-content">
                            <span class="place-detail-label">Note moyenne</span>
                            <strong>{{ place.note }} / 10</strong>
                        </div>
                    </div>

                    <div v-if="place.kilometres" class="place-detail">
                        <span class="place-detail-icon" aria-hidden="true"><i class="fa-solid fa-shoe-prints"></i></span>
                        <div class="place-detail-content">
                            <span class="place-detail-label">Distance</span>
                            <strong>{{ place.kilometres }} km</strong>
                        </div>
                    </div>

                    <div class="place-detail place-detail-coordinates">
                        <span class="place-detail-icon" aria-hidden="true"><i class="fa-solid fa-earth-europe"></i></span>
                        <div class="place-detail-content">
                            <span class="place-detail-label">Coordonnées GPS</span>
                            <strong>Latitude : {{ place.latitude }}</strong>
                            <span class="place-detail-secondary">Longitude : {{ place.longitude }}</span>
                        </div>
                    </div>
                </div>

                <router-link :to="`/uploadimages/${place.id}`"
                    class="btn btn-lg greenButton rounded place-details-action">
                    Proposer des images
                </router-link>
            </section>
        </div>

        <Map v-if="place" :singlePlace="place" />

        <h2 class="m-5">Avis sur ce lieu</h2>

        <div v-if="place.reviews.length > 0">

            <div class="mx-auto mb-4" id="averageRating">
                <div class="place-rating d-flex align-items-center justify-content-center gap-2">
                    <p class="place-rating-label mb-0 text-nowrap">moyenne : <span class="fw-bold">{{ place.note }}</span>/10</p>
                    <div class="place-rating-stars" role="meter" :aria-valuenow="ratingValue" aria-valuemin="0"
                        aria-valuemax="10" aria-label="Note moyenne sur 10">
                        <i v-for="star in 10" :key="star" class="fa-solid fa-star place-rating-star" aria-hidden="true"
                            :style="{ backgroundImage: `linear-gradient(to right, #ffc107 ${ratingFillForStar(star)}%, #1c6e8c ${ratingFillForStar(star)}%)` }"></i>
                    </div>
                </div>
            </div>

            <div class="reviews-list">
                <template v-for="(review, index) in place.reviews" :key="review.id">
                    <article v-if="index < 5 || showAllReviews" class="review-card">
                    <header class="review-card-header">
                        <div class="review-author">
                            <span class="review-avatar" aria-hidden="true">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <div class="review-author-info">
                                <span class="review-author-label">Avis de</span>
                                <strong>{{ review.user?.name || "Utilisateur" }}</strong>
                            </div>
                        </div>
                        <div class="review-meta">
                            <span class="review-score" :aria-label="`Note : ${review.note} sur 10`">
                                <i class="fa-solid fa-star" aria-hidden="true"></i>
                                {{ review.note }}<span class="review-score-max">/10</span>
                            </span>
                            <time class="review-date" :datetime="review.created_at">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                {{ moment(review.created_at).format("DD MMM YYYY [à] HH:mm") }}
                            </time>
                        </div>
                    </header>

                    <div class="review-card-body">
                        <p v-if="review.comment" class="review-comment">
                            {{ isReviewCommentExpanded(review.id)
                                ? review.comment
                                : review.comment.length > 500
                                    ? `${review.comment.substring(0, 500)}…`
                                    : review.comment }}
                        </p>
                        <p v-else class="review-empty">Aucun commentaire ajouté.</p>
                        <button v-if="review.comment?.length > 500" type="button" class="review-expand"
                            :aria-expanded="isReviewCommentExpanded(review.id)"
                            @click="toggleReviewComment(review.id)">
                            {{ isReviewCommentExpanded(review.id) ? "Lire moins" : "Lire la suite" }}
                        </button>
                    </div>
                    </article>
                </template>
            </div>

            <button v-if="place.reviews.length > 5" type="button" class="btn btn-lg greenButton rounded mb-3"
                @click="showAllReviews = !showAllReviews">
                {{ showAllReviews ? "Masquer les avis" : "Afficher tous les avis" }}
            </button>
        </div>

        <div v-else>
            <p>Aucun avis posté. Partagez le vôtre !</p>
        </div>

        <PosterAvis :place_id="place.id" />
    </div>
    <p v-else class="container py-5 text-center" role="alert">
        Impossible de charger les détails de ce lieu.
    </p>
</template>

<script setup>
import axios from 'axios'
import Map from '../utilities/Map.vue'
import LoadingSpinner from '../utilities/LoadingSpinner.vue'
import moment from 'moment';
import PosterAvis from '../utilities/CreateReview.vue';
import { useUserStore } from "../../stores/userStore"
import { useRoute } from 'vue-router';
import { onBeforeMount, computed, ref } from 'vue';

moment.locale('fr');

const userStore = useUserStore()
const route = useRoute()

const isInFavorites = computed(() => {
    return Array.isArray(userStore.favorites)
        && userStore.favorites.some(favorite => Number(favorite.id) === Number(place.value?.id))
})

const placeId = route.params.id
const place = ref(null)
const showAllReviews = ref(false)
const expandedReviewIds = ref(new Set())
const loading = ref(true)
const favoriteLoading = ref(false)
const favoriteError = ref("")
const ratingValue = computed(() => {
    const note = Number(place.value?.note)
    return Number.isFinite(note) ? Math.min(Math.max(note, 0), 10) : 0
})
const ratingFillForStar = star => Math.min(Math.max((ratingValue.value - star + 1) * 100, 0), 100)
const isReviewCommentExpanded = reviewId => expandedReviewIds.value.has(reviewId)
const toggleReviewComment = reviewId => {
    const expandedIds = new Set(expandedReviewIds.value)
    if (expandedIds.has(reviewId)) {
        expandedIds.delete(reviewId)
    } else {
        expandedIds.add(reviewId)
    }
    expandedReviewIds.value = expandedIds
}

const toggleFavorite = async () => {
    if (!place.value || favoriteLoading.value) return

    favoriteLoading.value = true
    favoriteError.value = ""

    try {
        if (isInFavorites.value) {
            await axios.delete(`/api/favorites/${userStore.id}/${place.value.id}`)
            userStore.storeFavorites(
                (Array.isArray(userStore.favorites) ? userStore.favorites : [])
                    .filter(favorite => Number(favorite.id) !== Number(place.value.id))
            )
        } else {
            await axios.post('/api/favorites', { place_id: place.value.id })
            const favorites = Array.isArray(userStore.favorites) ? userStore.favorites : []
            userStore.storeFavorites([
                ...favorites.filter(favorite => Number(favorite.id) !== Number(place.value.id)),
                place.value
            ])
        }
    } catch {
        favoriteError.value = "Impossible de mettre à jour vos favoris. Vérifiez votre connexion puis réessayez."
    } finally {
        favoriteLoading.value = false
    }
}

onBeforeMount(() => {
    // on récupère les infos du place (voir pour les passer en props)
    axios.get("/api/places/" + placeId)
        .then((response) => {
            place.value = response.data.data
            loading.value = false
        }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            loading.value = false
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
})

</script>

<style scoped>
.singlePicture {
    display: block;
    width: auto;
    max-width: 100%;
    height: 100vh;
    height: 100dvh;
    margin: 0 auto;
    object-fit: contain;
}

.place-gallery {
    width: 100%;
    height: 100vh;
    height: 100dvh;
}

.carousel-item img {
    object-fit: contain;
}

.carousel-inner,
.carousel-item {
    height: 100%;
}

.carousel-item img {
    width: 100%;
    height: 100%;
}

#texteFavoris {
    font-size: small;
}

.place-description {
    margin: 1rem;
    color: #27363b;
    font-size: 1.15rem;
    line-height: 1.7;
    text-align: left;
    white-space: pre-line;
}

.place-details {
    padding: clamp(1rem, 3vw, 2rem);
    border: 1px solid #dce5e8;
    border-radius: 1rem;
    background: #f7fafb;
    box-shadow: 0 0.25rem 1rem rgb(28 110 140 / 8%);
}

.place-details-title {
    margin: 0 0 1.25rem;
    color: #24343a;
    font-size: clamp(1.25rem, 2vw, 1.6rem);
    text-align: left;
}

.place-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
    gap: 0.85rem;
}

.place-detail {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 0.9rem;
    padding: 1rem;
    border: 1px solid #e3eaec;
    border-radius: 0.75rem;
    background: #fff;
}

.place-detail-icon {
    display: grid;
    width: 2.75rem;
    height: 2.75rem;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 0.75rem;
    background: #e7f4f0;
    color: #1c6e8c;
    font-size: 1.15rem;
}

.place-detail-content {
    display: grid;
    min-width: 0;
    gap: 0.15rem;
    color: #27363b;
    text-align: left;
    overflow-wrap: anywhere;
}

.place-detail-label {
    color: #64757b;
    font-size: 0.8rem;
}

.place-detail-secondary {
    color: #45575e;
}

.place-details-action {
    display: inline-block;
    margin-top: 1rem;
}

.reviews-list {
    display: grid;
    gap: 1rem;
    width: min(100% - 2rem, 960px);
    margin: 0 auto 1.5rem;
    text-align: left;
}

.review-card {
    overflow: hidden;
    border: 1px solid #dce5e8;
    border-radius: 1rem;
    background: #fff;
    box-shadow: 0 0.25rem 1rem rgb(28 110 140 / 10%);
}

.review-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e8eef0;
    background: #f7fafb;
}

.review-author,
.review-meta,
.review-score,
.review-date {
    display: flex;
    align-items: center;
}

.review-author {
    min-width: 0;
    gap: 0.75rem;
}

.review-avatar {
    display: grid;
    width: 2.75rem;
    height: 2.75rem;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 50%;
    background: #e7f4f0;
    color: #1c6e8c;
    font-size: 1.1rem;
}

.review-author-info {
    display: grid;
    min-width: 0;
    gap: 0.1rem;
    color: #24343a;
}

.review-author-info strong {
    overflow-wrap: anywhere;
}

.review-author-label {
    color: #64757b;
    font-size: 0.8rem;
}

.review-meta {
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 0.75rem 1rem;
    color: #45575e;
    font-size: 0.9rem;
}

.review-score {
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    border-radius: 999px;
    background: #fff4cf;
    color: #775600;
    font-weight: 700;
    white-space: nowrap;
}

.review-score > i {
    color: #d99a00;
}

.review-score-max {
    color: #775600;
    font-weight: 400;
}

.review-date {
    gap: 0.45rem;
}

.review-date > i {
    color: #1c6e8c;
}

.review-card-body {
    padding: 1.25rem;
}

.review-comment {
    margin: 0;
    color: #27363b;
    font-size: 1rem;
    line-height: 1.7;
    overflow-wrap: anywhere;
    white-space: pre-line;
}

.review-empty {
    margin: 0;
    color: #68787d;
    font-style: italic;
}

.review-expand {
    margin-top: 0.75rem;
    padding: 0;
    border: 0;
    background: transparent;
    color: #1c6e8c;
    font-weight: 600;
    text-decoration: underline;
    text-underline-offset: 0.2em;
}

.review-expand:hover {
    color: #12495d;
}

.place-rating {
    width: 100%;
    flex-wrap: nowrap;
    gap: 0.35rem !important;
}

.place-rating-label {
    font-size: clamp(0.85rem, 1.2vw, 1.05rem);
}

.place-rating-stars {
    display: flex;
    justify-content: center;
    gap: 0.05rem;
    flex-shrink: 0;
}

.place-rating-star {
    font-size: clamp(0.8rem, 1.1vw, 1rem);
    color: transparent;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: none;
    text-shadow: none !important;
}

#detailsLieu i {
    color: #1c6e8c
}

#averageRating p {
    font-size: 16px !important;
}

@media (max-width: 767.98px) {
    .place-description {
        margin: 0.75rem 0.25rem 1.25rem;
        font-size: 1rem;
    }

    .place-details {
        padding: 1rem;
    }

    .place-details-grid {
        grid-template-columns: 1fr;
        gap: 0.65rem;
    }

    .place-detail {
        padding: 0.85rem;
    }

    .place-details-action {
        display: block;
        width: 100%;
    }

    .reviews-list {
        width: calc(100% - 1.5rem);
        gap: 0.75rem;
    }

    .review-card-header {
        align-items: flex-start;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem;
    }

    .review-meta {
        justify-content: center;
        width: 100%;
    }

    .review-card-body {
        padding: 1rem;
    }

    .singlePicture {
        width: 100%;
        height: auto;
        aspect-ratio: 3 / 4;
        object-fit: cover;
    }

    .place-gallery {
        height: auto;
        aspect-ratio: 3 / 4;
        overflow: hidden;
    }

    .carousel-item img {
        object-fit: cover;
    }
}
</style>