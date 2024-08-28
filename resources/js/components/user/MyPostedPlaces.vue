<template>

    <div class="pt-5 pb-2">
        <i class="mx-auto fa-3x greenIcon fa-solid fa-paper-plane"></i>
        <h1 class="mt-2">Mes lieux postés</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">

        <div class="row">

            <div v-if="userStore.userPlaces.length == 0"><i class="greenIcon fa-solid fa-xmark fa-5x"></i>
                <p>Vous n'avez posté aucun lieu</p>
                <router-link to="/proposerlieu"><button class="btn btn-lg mt-3 greenButton rounded-pill">Proposer un
                        lieu</button>
                </router-link>
            </div>

            <div v-else class="col-lg-6 border border-3 border-white" v-for="userPlace in userStore.userPlaces"
                :key="userPlace.id">
                <PlaceCard :lieu="userPlace" />
            </div>

        </div>
    </div>

</template>

<script setup>
import { useUserStore } from "../../stores/userStore";
import PlaceCard from "../utilities/PlaceCard.vue";
import { onBeforeMount } from "vue";

const store = useUserStore()

// on récupère les lieux postes par le user
const getPostedPlaces = () => {
    axios.post("/api/places/getplacesbyuser", null, {
        params: {
            user_id: userStore.id
        }
    })
        .then(response => userStore.storeUserPlaces(response.data.data))
        .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
}

onBeforeMount(() => {
    // on récupère les lieux postés par l'utilisateur
    getPostedPlaces()
})
</script>
