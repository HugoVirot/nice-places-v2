<template>
    <div class="p-3">
        <i class="greenIcon mx-auto fa-2x fa-solid fa-user"></i>
        <h1 class="mt-2">Mes lieux favoris</h1>
    </div>

    <LoadingSpinner v-if="loading" />

    <section v-else-if="!userStore.userLoggedIn" class="p-5">
        <p>Connectez-vous pour consulter vos lieux favoris.</p>
        <router-link to="/connexion">
            <button class="btn btn-lg greenButton rounded-pill">Se connecter</button>
        </router-link>
    </section>

    <section v-else-if="error" class="p-5" role="alert">
        <p>{{ error }}</p>
    </section>

    <section v-else-if="favoritePlaces.length">
        <Sortings :places="favoritePlaces" @placesSorted="updatePlaces" />

        <div class="container-fluid p-3 p-lg-5">
            <div class="row">
                <div v-for="favorite in favoritePlaces" :key="favorite.id"
                    class="col-lg-6 border border-3 border-white">
                    <PlaceCard :place="favorite" />
                </div>
            </div>
        </div>
    </section>

    <section v-else class="p-5">
        <i class="greenIcon fa-solid fa-xmark fa-5x"></i>
        <p>Vous n'avez aucun lieu dans vos favoris.</p>
    </section>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useUserStore } from "../../stores/userStore"
import LoadingSpinner from "../utilities/LoadingSpinner.vue"
import PlaceCard from "../utilities/PlaceCard.vue"
import Sortings from "../utilities/Sortings.vue"

const userStore = useUserStore()
const favoritePlaces = ref([])
const loading = ref(true)
const error = ref('')

const updatePlaces = places => {
    favoritePlaces.value = places
}

onMounted(async () => {
    if (!userStore.userLoggedIn || !userStore.id) {
        loading.value = false
        return
    }

    try {
        const response = await axios.get(`/api/favorites/${userStore.id}`)
        const favorites = response.data.data
        if (!Array.isArray(favorites)) {
            throw new Error('Invalid favorites response')
        }

        userStore.storeFavorites(favorites)
        favoritePlaces.value = favorites
    } catch {
        error.value = "Impossible de charger vos favoris. Vérifiez votre connexion puis réessayez."
    } finally {
        loading.value = false
    }
})
</script>
