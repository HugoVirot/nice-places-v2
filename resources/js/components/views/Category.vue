<template>
    <p v-if="error" class="p-5 text-danger" role="alert">{{ error }}</p>

    <template v-else-if="category">
        <div class="container-fluid px-5" :class="displayedPlaces.length ? 'py-5' : 'py-2'"
            :style="`background-image: url(/images/category${category.id}.jpg); background-position: center; background-size: cover;`">
            <div class="m-auto">
                <span class="fa-5x iconWithShadow" v-html="category.icon" :style="{ color: category.color }"></span>
                <h1 class="textWithShadow bigFontSize2 text-white">{{ category.name }}</h1>
            </div>
        </div>

        <Tris v-if="displayedPlaces.length" :places="displayedPlaces" @placesSorted="updatePlaces" />
        <Filters :places="displayedPlaces" :unfilteredPlaces="unfilteredPlaces" @filterApplied="updatePlaces" />

        <div class="container-fluid mt-2" :class="displayedPlaces.length ? 'p-3 p-lg-5' : 'p-2'">
            <p class="pb-2 fs-3">
                {{ displayedPlaces.length ? `${displayedPlaces.length} lieu(x) trouvé(s)` : "aucun lieu trouvé" }}
            </p>

            <div class="row">
                <div v-for="place in displayedPlaces" :key="place.id"
                    class="col-lg-6 border border-3 border-white">
                    <PlaceCard :place="place" />
                </div>
            </div>
        </div>
    </template>

    <LoadingSpinner v-else-if="loading" />
</template>

<script setup>
import axios from "axios"
import { onBeforeMount, ref } from "vue"
import { useRoute } from "vue-router"
import Filters from "../utilities/Filters.vue"
import Tris from "../utilities/Sortings.vue"
import PlaceCard from "../utilities/PlaceCard.vue"
import LoadingSpinner from "../utilities/LoadingSpinner.vue"

const category = ref(null)
const displayedPlaces = ref([])
const unfilteredPlaces = ref([])
const loading = ref(true)
const error = ref("")
const route = useRoute()

const updatePlaces = places => {
    displayedPlaces.value = places
}

onBeforeMount(async () => {
    try {
        const response = await axios.get(`/api/categories/${route.params.id}`)
        const result = response.data.data
        if (!result || !Array.isArray(result.places)) {
            throw new Error("Invalid category response")
        }

        category.value = result
        unfilteredPlaces.value = result.places.filter(place => place.status === "validé")
        displayedPlaces.value = [...unfilteredPlaces.value]
    } catch {
        error.value = "Impossible de charger les lieux de cette catégorie. Vérifiez votre connexion puis réessayez."
    } finally {
        loading.value = false
    }
})
</script>
