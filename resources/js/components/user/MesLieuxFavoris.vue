<template>

    <div class="p-3">
        <i class="greenIcon mx-auto fa-2x fa-solid fa-user"></i>
        <h1 class="mt-2">Mes lieux favoris</h1>
    </div>

    <section v-if="favoritesList && favoritesList.length > 0">

        <Filtres :places="favoritesList" :unsortedPlaces="unfilteredFavorites" @filterApplied="updatePlaces" />

        <Tris :places="favoritesList" @sortedPlaces="updatePlaces" />

        <div class="container-fluid p-3 p-lg-5">

            <div class="row">
                <div class="col-lg-6 border border-3 border-white" v-for="favorite in favoritesList" :key="favorite.id">
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
import { useUserStore } from "../../stores/userStore"
import Filters from "../utilities/Filtres.vue"
import Tris from "../utilities/Tris.vue"
import PlaceCard from "../utilities/PlaceCard.vue"
import { onBeforeMount } from "vue";

const favoritesList = ref([])
const unfilteredFavorites = ref([])

// récupérer les favoris de l'utilisateur
const getFavorites = () => {

    axios.get('/api/favoris/' + userStore.id)
        .then(response => {
            userStore.storeFavorites(response.data.data);
        }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
},

const updatePlaces = sortedPlaces => {  // déclenchée si filtre appliqué via composant enfant Filtres
    favoritesList.value = sortedPlaces // on remplace les lieux de la catégorie par les lieux filtrés 
}

onBeforeMount(() => {

    // on récupère les lieux postés par l'utilisateur
    userStore.getFavorites()

    // on les stocke dans la variable favoritesList pour pouvoir les trier (impossible de modifier une propriété computed / lecture seule)
    favoritesList.value = favorites

    // on stocke les favoris dans une autre variable pour conserver la liste de base en cas de tri
    unfilteredFavorites.value = favorites
})
</script>