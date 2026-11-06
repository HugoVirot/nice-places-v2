<script setup>
import axios from "axios";
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useUserStore } from '../../stores/userStore'
import { usePlacesStore } from "../../stores/placesStore";
import { useRouter } from "vue-router";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";

const userStore = useUserStore()
const placesStore = usePlacesStore()
const router = useRouter()

const name = ref("")
const description = ref("")
const latitude = ref("")
const longitude = ref("")
const category = ref("")
const note = ref("")
const time = ref("")
const difficulty = ref("")
const kilometres = ref("")
const department = ref("")
const adress = ref("")
const postcode = ref("")
const city = ref("")
const locationQuery = ref("")
const locationResults = ref([])
const selectedLocation = ref(null)
const searchingLocations = ref(false)
const locationSearchError = ref("")
const validationErrors = ref(null)
const submitting = ref(false)
const userLoggedIn = computed(() => userStore.userLoggedIn)
const categories = computed(() => placesStore.categories ?? [])
const departments = computed(() => placesStore.departments ?? [])
let searchTimer
let searchController

watch(locationQuery, query => {
    selectedLocation.value = null
    latitude.value = ""
    longitude.value = ""
    department.value = ""
    adress.value = ""
    postcode.value = ""
    city.value = ""
    locationSearchError.value = ""
    locationResults.value = []
    searchingLocations.value = false
    clearTimeout(searchTimer)
    searchController?.abort()

    if (query.trim().length < 3) {
        searchingLocations.value = false
        return
    }

    searchTimer = setTimeout(async () => {
        const controller = new AbortController()
        searchController = controller
        searchingLocations.value = true
        try {
            const response = await axios.get("/api/places/geocode", {
                params: { q: query.trim() },
                signal: controller.signal
            })
            if (!controller.signal.aborted) {
                locationResults.value = response.data.data ?? []
                if (!locationResults.value.length) {
                    locationSearchError.value = "Aucun résultat. Essayez un nom de lieu ou une adresse plus précise."
                }
            }
        } catch (requestError) {
            if (!controller.signal.aborted) {
                locationSearchError.value = requestError.response?.data?.message
                    || "La recherche de lieu est indisponible. Réessayez dans quelques instants."
            }
        } finally {
            if (!controller.signal.aborted) searchingLocations.value = false
        }
    }, 350)
})

const firstValue = value => Array.isArray(value) ? value[0] : value

const locationLabel = feature => {
    const properties = feature.properties
    const title = firstValue(properties.name) || properties.toponym || properties.label
    const town = firstValue(properties.city)
    return properties.label || [title, town].filter(Boolean).join(", ")
}

const selectLocation = feature => {
    const properties = feature.properties
    const point = feature.geometry?.coordinates
    const postalCode = firstValue(properties.postcode)
    const departmentCode = firstValue(properties.depcode) || postalCode?.slice(0, 2)
    const matchedDepartment = departments.value.find(item => item.code === departmentCode)
    const town = firstValue(properties.city)

    if (!Array.isArray(point) || point.length < 2 || !postalCode || !town || !matchedDepartment) {
        locationSearchError.value = "Ce résultat ne contient pas assez d’informations. Choisissez une adresse plus précise."
        return
    }

    const label = locationLabel(feature)
    selectedLocation.value = { label }
    latitude.value = Number(point[1]).toFixed(7)
    longitude.value = Number(point[0]).toFixed(7)
    department.value = matchedDepartment.id
    adress.value = (properties.street || firstValue(properties.name) || properties.toponym || properties.label || label).slice(0, 75)
    postcode.value = postalCode
    city.value = town
    locationSearchError.value = ""
    locationResults.value = []
}

onBeforeUnmount(() => {
    clearTimeout(searchTimer)
    searchController?.abort()
})

onMounted(async () => {
    try {
        const [categoryResponse, departmentResponse] = await Promise.all([
            placesStore.categories?.length ? Promise.resolve(null) : axios.get("/api/categories"),
            placesStore.departments?.length ? Promise.resolve(null) : axios.get("/api/departments")
        ])
        if (categoryResponse) placesStore.storeCategories(categoryResponse.data.data)
        if (departmentResponse) placesStore.storeDepartments(departmentResponse.data.data)
    } catch (requestError) {
        validationErrors.value = {
            general: [requestError.response?.data?.message || "Impossible de charger les catégories et départements."]
        }
    }
})

const sendData = async () => {
    if (!category.value || !selectedLocation.value) {
        validationErrors.value = {
            general: ["Choisissez une catégorie et sélectionnez un lieu dans les résultats de recherche."]
        }
        return
    }

    submitting.value = true
    validationErrors.value = null
    try {
        const response = await axios.post("/api/places", {
            name: name.value,
            description: description.value || null,
            latitude: latitude.value,
            longitude: longitude.value,
            category_id: category.value,
            note: note.value || null,
            time: time.value || null,
            difficulty: difficulty.value || null,
            kilometres: kilometres.value || null,
            department_id: department.value,
            adress: adress.value,
            postcode: postcode.value,
            city: city.value
        })
        placesStore.storeNewPlace(response.data.data)
        await router.push(`/successmessage/uploadimages/${encodeURIComponent(response.data.message)}/${response.data.data.id}`)
    } catch (requestError) {
        validationErrors.value = requestError.response?.data?.errors || {
            general: [requestError.response?.data?.message || "Impossible d'enregistrer le lieu. Réessayez."]
        }
    } finally {
        submitting.value = false
    }
}

</script>

<template>

    <div class="p-5">
        <i class="mx-auto greenIcon fa-3x fa-solid fa-paper-plane"></i>
        <h1 class="mt-2">Proposer un lieu</h1>
    </div>

    <div v-if="userLoggedIn" class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white">Partagez vos coups de cœur avec nous !</div>

                    <div class="card-body">

                        <form @submit.prevent="sendData">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du lieu <span class="text-danger">*</span></label>
                                <input v-model.trim="name" id="name" type="text" class="form-control" required
                                    minlength="3" maxlength="100" autocomplete="off" autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <select id="category" v-model="category" class="form-select" required>
                                    <option disabled value="">Choisir une catégorie</option>
                                    <option v-for="item in categories" :key="item.id" :value="item.id">{{ item.name }}</option>
                                </select>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="locationQuery" class="form-label">Où se trouve ce lieu ? <span class="text-danger">*</span></label>
                                <input id="locationQuery" v-model="locationQuery" type="search" class="form-control"
                                    placeholder="Rechercher un lieu ou une adresse" autocomplete="off" required minlength="3"
                                    role="combobox" aria-autocomplete="list" aria-controls="location-results"
                                    :aria-expanded="locationResults.length > 0">
                                <div class="form-text">Saisissez quelques lettres puis choisissez le bon résultat. Les coordonnées sont ajoutées automatiquement.</div>
                                <div v-if="searchingLocations" class="form-text" role="status">Recherche en cours…</div>
                                <div v-if="locationResults.length" id="location-results" class="list-group location-results" role="listbox">
                                    <button v-for="(result, index) in locationResults" :key="result.properties.id || index"
                                        type="button" class="list-group-item list-group-item-action"
                                        role="option" @click="selectLocation(result)">
                                        {{ locationLabel(result) }}
                                    </button>
                                </div>
                                <p v-if="locationSearchError" class="text-danger small mt-2 mb-0" role="status">{{ locationSearchError }}</p>
                            </div>

                            <div v-if="selectedLocation" class="alert alert-success py-2" role="status">
                                <strong>Lieu sélectionné :</strong> {{ selectedLocation.label }}
                                <div class="small">{{ city }} ({{ postcode }}) · {{ departments.find(item => item.id === department)?.name }}
                                    <span class="d-block">Position : {{ latitude }}, {{ longitude }}</span>
                                </div>
                            </div>

                            <details class="mb-3">
                                <summary class="text-primary">Ajouter des détails facultatifs</summary>
                                <div class="pt-3">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea v-model.trim="description" id="description" class="form-control"
                                            maxlength="3000" rows="3" placeholder="Qu’est-ce qui rend ce lieu intéressant ?"></textarea>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-4">
                                            <label for="note" class="form-label">Votre note /10</label>
                                            <input v-model="note" id="note" min="1" max="10" step="0.1" type="number" class="form-control">
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="time" class="form-label">Durée (heures)</label>
                                            <input v-model="time" id="time" min="1" max="24" type="number" class="form-control">
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="difficulty" class="form-label">Difficulté</label>
                                            <select v-model="difficulty" id="difficulty" class="form-select">
                                                <option value="">Non précisée</option>
                                                <option value="famille">Famille</option>
                                                <option value="amateur">Amateur</option>
                                                <option value="sportif">Sportif</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="kilometres" class="form-label">Distance (km)</label>
                                            <input v-model="kilometres" id="kilometres" min="1" max="100" type="number" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <p class="form-text">La recherche d’adresse est fournie par le service de géocodage de la Géoplateforme (IGN).</p>
                            <div class="text-center mt-4">
                                <button type="submit" :disabled="submitting || !categories.length || !selectedLocation"
                                    class="btn btn-lg greenButton rounded-pill text-light">
                                    {{ submitting ? "Envoi en cours…" : "Proposer ce lieu" }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="container">
        <p class="mb-2">Vous devez être connecté pour proposer un place.</p>
        <router-link to="/connexion"><button class="btn btn-lg greenButton mb-3 rounded-pill">Se connecter</button>
        </router-link>

        <p class="mb-2">Pas encore inscrit ?</p>
        <router-link to="/inscription"><button class="btn btn-lg greenButton rounded-pill">Créer un compte</button>
        </router-link>
    </div>

</template>

<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/riviere.jpg);
    background-position: center;
    background-size: cover;
}

.location-results {
    position: absolute;
    z-index: 10;
    width: 100%;
    max-height: 16rem;
    overflow-y: auto;
}
</style>