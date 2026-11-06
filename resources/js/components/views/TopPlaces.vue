<template>
    <section id="topLieux" class="p-2 text-secondary">
        <div class="p-3">
            <i class="greenIcon mx-auto fa-3x fa-solid fa-list"></i>
            <h1 class="mt-2">Liste des lieux</h1>
        </div>

        <p v-if="error" class="text-danger" role="alert">{{ error }}</p>
        <Filtres v-else-if="!loading" :places="topPlaces" :unfilteredPlaces="topUnfilteredPlaces"
            @filterApplied="updatePlaces" />

        <div class="container-fluid p-3 p-lg-5">
            <p v-if="loading">Chargement des lieux...</p>

            <div v-else-if="topPlaces.length" class="row">
                <div v-for="(topPlace, index) in topPlaces" :key="topPlace.id"
                    class="col-lg-6 border border-3 border-white textWithShadow">
                    <PlaceCard :place="topPlace" :index="index" />
                </div>
            </div>

            <p v-else-if="!error">Aucun lieu ne correspond à ce filtre.</p>
        </div>
    </section>
</template>

<script setup>
import axios from "axios"
import { onBeforeMount, ref } from "vue"
import { usePlacesStore } from "../../stores/placesStore"
import Filtres from "../utilities/Filters.vue"
import PlaceCard from "../utilities/PlaceCard.vue"

const placesStore = usePlacesStore()
const topPlaces = ref([])
const topUnfilteredPlaces = ref([])
const loading = ref(true)
const error = ref("")

const updatePlaces = places => {
    topPlaces.value = places
}

onBeforeMount(async () => {
    try {
        const response = await axios.get("/api/places")
        const places = response.data.data
        if (!Array.isArray(places)) {
            throw new Error("Invalid places response")
        }

        placesStore.storePlaces(places)
        const rankedPlaces = [...places].sort((a, b) => Number(a.note) - Number(b.note))
        topPlaces.value = rankedPlaces
        topUnfilteredPlaces.value = rankedPlaces
    } catch {
        error.value = "Impossible de charger le classement des lieux. Vérifiez votre connexion puis réessayez."
    } finally {
        loading.value = false
    }
})
</script>
