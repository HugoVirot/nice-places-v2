<template>
    <section id="topLieux" class="p-2 text-secondary">
        <div class="p-3">
            <i class="greenIcon mx-auto fa-3x fa-solid fa-message"></i>
            <h1 class="mt-2">Top 100 des lieux</h1>
        </div>

        <Filtres :places="topPlaces" :unfilteredPlaces="topUnfilteredPlaces" @filterApplied="updatePlaces" />

        <div class="container-fluid p-3 p-lg-5">

            <div v-if="topLieux" class="row">

                <div v-if="topPlaces.length > 0" class="col-lg-6 border border-3 border-white textWithShadow"
                    v-for="(topPlace, index) in topPlaces" :key="topLieu.id">
                    <PlaceCard :place="topPlace" :index="index" />
                </div>

                <div v-else>
                    <p>Aucun lieu de ce département dans le top 100.</p>
                </div>

            </div>

            <div v-else>
                <p>chargement en cours...</p>
            </div>

        </div>
    </section>
</template>

<script setup>
import Filtres from "../utilities/Filtres.vue"
import PlaceCard from '../utilities/PlaceCard.vue'
import { usePlacesStore } from '../../stores/placesStore';
import { onBeforeMount } from "vue";

const placesStore = usePlacesStore()

const topPlaces = ref('')
const topUnfilteredPlaces = ref('')

const updatePlaces = sortedPlaces => {  // déclenchée si filtre appliqué via composant enfant Filtres
    topPlaces.value = sortedPlaces // on remplace le classement affiché par les lieux filtrés 
}

onBeforeMount(() => {
    // on appelle le getter getTopRatedPlaces pour récupérer les 100 lieux les mieux notés
    // on les stocke dans la variable toplieux des datas
    topPlaces = placesStore.getTopRatedPlaces
    // on garde une copie de cette liste des 100 lieux les mieux notés en cas de retour
    // à l'affichage normal après avoir effectué un ou plusieurs tris
    topUnfilteredPlaces = this.topPlaces
})
</script>
