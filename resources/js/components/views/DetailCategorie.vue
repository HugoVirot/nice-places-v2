<template>

    <div v-if="category" class="container-fluid p-5"
        :style="`background-image: url(/images/category${category.id}.jpg); background-position: center; background-size: cover;`">

        <div class="m-auto">
            <span class="fa-5x iconWithShadow" v-html="category.icon" :style="{ color: category.color }"></span>
            <h1 class="textWithShadow bigFontSize2 text-white">{{ category.name }}</h1>
        </div>

    </div>

    <Tris :places="category.places" @placesSorted="updatePlaces" />

    <Filters :places="category.places" :unfilteredPlaces="unfilteredPlaces" @filterApplied="updatePlaces" />

    <div class="container-fluid p-3 p-lg-5 mt-3">

        <div v-if="!this.loading">
            <p v-if="category.places.length > 0" class="pb-2 fs-3">{{ category.places.length }} lieu(x) trouvé(s)</p>
            <p v-else class="pb-2 fs-3">aucun lieu trouvé</p>
        </div>

        <div class="row">
            <!-- le composant PlaceCard affiche chaque place (qu'il reçoit en props)-->
            <div class="col-lg-6 border border-3 border-white" v-for="place in category.places" :key="place.id">
                <PlaceCard :place="place" />
            </div>
        </div>
    </div>

</template>

<script setup>
import axios from "axios";
import Filters from "../utilities/Filtres.vue"
import Tris from "../utilities/Tris.vue"
import PlaceCard from "../utilities/PlaceCard.vue";
import { onBeforeMount } from "vue";

const category = ref('')
const loading = ref(true)
const unfilteredPlaces = ref('')

const updatePlaces = filteredOrSortedPlaces => {  // déclenchée si tri ou filtre appliqué via composant enfant Tris ou  Filtres
    category.places = filteredOrSortedPlaces // on remplace les lieux de la catégorie par les lieux triés ou filtrés 
},

// filtre les places en ne gardant que ceux validés
const getValidatedPlaces = () => {
    category.places = category.places.filter(place => place.status == "validé")
}

// on récupère la catégorie en fonction de l'id passé en paramètre de la route
onBeforeMount(() => {

    axios.get("/api/categories/" + $route.params.id)
        .then(response => {
            category.value = response.data
            getValidatedPlaces()
            loading.value = false
            unfilteredPlaces = category.places

        }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
})
</script>