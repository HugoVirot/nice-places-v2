<template>
    <div class="pt-5 pb-2 text-center">
        <i class="mx-auto fa-3x greenIcon fa-solid fa-paper-plane"></i>
        <h1 class="mt-2">Mes lieux postés</h1>
    </div>

    <LoadingSpinner v-if="loading" />

    <section v-else-if="!userStore.userLoggedIn" class="container p-5 text-center">
        <p>Connectez-vous pour consulter les lieux que vous avez proposés.</p>
        <router-link to="/connexion">
            <button class="btn btn-lg greenButton rounded-pill">Se connecter</button>
        </router-link>
    </section>

    <section v-else-if="error" class="container p-5 text-center" role="alert">
        <p>{{ error }}</p>
        <button type="button" class="btn greenButton rounded-pill" @click="loadPostedPlaces">Réessayer</button>
    </section>

    <section v-else-if="!postedPlaces.length" class="container p-5 text-center">
        <i class="greenIcon fa-solid fa-xmark fa-5x" aria-hidden="true"></i>
        <p>Vous n'avez posté aucun lieu.</p>
        <router-link to="/proposerlieu">
            <button class="btn btn-lg mt-3 greenButton rounded-pill">Proposer un lieu</button>
        </router-link>
    </section>

    <section v-else class="container-fluid p-3 p-lg-5">
        <div class="row">
            <div v-for="place in postedPlaces" :key="place.id" class="col-lg-6 border border-3 border-white mb-4">
                <p class="mb-2">
                    <span class="status-badge" :class="statusClass(place.status)">
                        <span class="status-dot" aria-hidden="true"></span>
                        {{ place.status }}
                    </span>
                </p>
                <PlaceCard :place="place" />
            </div>
        </div>
    </section>
</template>

<script setup>
import axios from "axios"
import { onMounted, ref } from "vue"
import { useUserStore } from "../../stores/userStore"
import LoadingSpinner from "../utilities/LoadingSpinner.vue"
import PlaceCard from "../utilities/PlaceCard.vue"

const userStore = useUserStore()
const postedPlaces = ref([])
const loading = ref(true)
const error = ref("")

const statusClass = status => {
    if (status === "validé") return "status-approved"
    if (status === "en attente") return "status-pending"
    if (status === "à modifier") return "status-changes"
    if (status === "refusé") return "status-rejected"
    return "status-neutral"
}

const loadPostedPlaces = async () => {
    if (!userStore.userLoggedIn || !userStore.id) {
        loading.value = false
        return
    }

    loading.value = true
    error.value = ""
    try {
        const response = await axios.post("/api/lieus/getplacesbyuser")
        if (!Array.isArray(response.data.data)) {
            throw new Error("Invalid posted places response")
        }
        postedPlaces.value = response.data.data
        userStore.storeUserPlaces(response.data.data)
    } catch {
        error.value = "Impossible de charger vos lieux. Vérifiez votre connexion puis réessayez."
    } finally {
        loading.value = false
    }
}

onMounted(loadPostedPlaces)
</script>

<style scoped>
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-dot {
    width: 0.55rem;
    height: 0.55rem;
    border-radius: 50%;
    background-color: currentColor;
}

.status-pending { color: #7a5700; background-color: #fff0bf; }
.status-changes { color: #80500a; background-color: #ffe2b8; }
.status-rejected { color: #8e2632; background-color: #f8d7da; }
.status-approved { color: #17613f; background-color: #d6f1e4; }
.status-neutral { color: #435b64; background-color: #e6ecee; }
</style>
