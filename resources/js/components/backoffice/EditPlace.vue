<template>
    <main class="container-fluid p-3 p-lg-5">
        <LoadingSpinner v-if="loading" />

        <section v-else-if="loadError" class="container p-5 text-center" role="alert">
            <p class="alert alert-danger">{{ loadError }}</p>
            <router-link :to="backPath" class="btn btn-outline-secondary rounded-pill">Retour</router-link>
        </section>

        <section v-else-if="!canEdit" class="container p-5 text-center" role="alert">
            <h1>Modification impossible</h1>
            <p>Seul l’auteur du lieu ou un administrateur peut le modifier.</p>
            <router-link to="/meslieuxpostes" class="btn btn-outline-secondary rounded-pill">Retour à mes lieux</router-link>
        </section>

        <template v-else>
            <header class="p-3 text-center">
                <i class="fa-3x greenIcon fa-solid fa-pen-to-square" aria-hidden="true"></i>
                <h1 class="mt-2">Modifier {{ place.name }}</h1>
                <p v-if="isAdmin && place.user" class="mb-2">Posté par {{ place.user.name }}</p>
                <span class="status-badge" :class="statusClass(place.status)">{{ place.status }}</span>
                <p v-if="!isAdmin && place.comment" class="alert alert-warning mt-3" role="note">
                    Message de l’administration : {{ place.comment }}
                </p>
            </header>

            <ValidationErrors v-if="validationErrors" :errors="validationErrors" />

            <div class="row justify-content-center p-2 p-lg-4">
                <div class="col-md-9 col-lg-8">
                    <div class="card">
                        <div class="card-header text-white">Modifiez les informations du lieu</div>
                        <div class="card-body">
                            <form @submit.prevent="saveChanges">
                                <div v-if="isAdmin" class="mb-3">
                                    <label for="status" class="form-label">Statut</label>
                                    <select id="status" v-model="form.status" class="form-select" required>
                                        <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                                    </select>
                                </div>

                                <div v-if="isAdmin && ['à modifier', 'refusé'].includes(form.status)" class="mb-3">
                                    <label for="comment" class="form-label">Message à l’auteur / raison du refus</label>
                                    <textarea id="comment" v-model.trim="form.comment" class="form-control" rows="3"
                                        maxlength="1000" minlength="5" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom du lieu</label>
                                    <input id="name" v-model.trim="form.name" type="text" class="form-control"
                                        minlength="10" maxlength="100" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" v-model.trim="form.description" class="form-control"
                                        minlength="25" maxlength="3000" rows="4" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Catégorie</label>
                                    <select id="category" v-model="form.category_id" class="form-select" required>
                                        <option disabled value="">Choisir une catégorie</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3 position-relative">
                                    <label for="locationQuery" class="form-label">Rechercher une adresse ou un lieu</label>
                                    <input id="locationQuery" v-model="locationQuery" type="search" class="form-control"
                                        placeholder="Saisissez quelques lettres puis choisissez un résultat"
                                        autocomplete="off" role="combobox" aria-autocomplete="list"
                                        aria-controls="location-results"
                                        :aria-expanded="locationResults.length > 0">
                                    <div class="form-text">
                                        Sélectionnez un résultat pour mettre à jour automatiquement l’adresse, la ville,
                                        le département et les coordonnées. Vous pouvez aussi modifier ces champs manuellement.
                                    </div>
                                    <div v-if="searchingLocations" class="form-text" role="status">Recherche en cours…</div>
                                    <div v-if="locationResults.length" id="location-results"
                                        class="list-group location-results" role="listbox">
                                        <button v-for="(result, index) in locationResults"
                                            :key="result.properties?.id || index" type="button"
                                            class="list-group-item list-group-item-action" role="option"
                                            @click="selectLocation(result)">
                                            {{ locationLabel(result) }}
                                        </button>
                                    </div>
                                    <p v-if="locationSearchError" class="text-danger small mt-2 mb-0" role="status">
                                        {{ locationSearchError }}
                                    </p>
                                    <p class="form-text">Recherche fournie par le service de géocodage de la Géoplateforme (IGN).</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input id="latitude" v-model="form.latitude" type="number" step="any"
                                            min="-90" max="90" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input id="longitude" v-model="form.longitude" type="number" step="any"
                                            min="-180" max="180" class="form-control" required>
                                    </div>
                                </div>

                                <div v-if="isAdmin" class="mb-3">
                                    <label for="note" class="form-label">Note sur 10</label>
                                    <input id="note" v-model="form.note" type="number" min="1" max="10" step="0.1"
                                        class="form-control" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="time" class="form-label">Durée moyenne (heures)</label>
                                        <input id="time" v-model="form.time" type="number" min="1" max="24"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="difficulty" class="form-label">Niveau de difficulté</label>
                                        <select id="difficulty" v-model="form.difficulty" class="form-select" required>
                                            <option value="famille">Famille</option>
                                            <option value="amateur">Amateur</option>
                                            <option value="sportif">Sportif</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="kilometres" class="form-label">Distance moyenne (km, facultatif)</label>
                                    <input id="kilometres" v-model="form.kilometres" type="number" min="1" max="100"
                                        class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="department" class="form-label">Département</label>
                                    <select id="department" v-model="form.department_id" class="form-select" required>
                                        <option disabled value="">Choisir un département</option>
                                        <option v-for="department in departments" :key="department.id"
                                            :value="department.id">
                                            {{ department.code }} - {{ department.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="adress" class="form-label">Adresse</label>
                                    <input id="adress" v-model.trim="form.adress" type="text" class="form-control"
                                        minlength="5" maxlength="75" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="postcode" class="form-label">Code postal</label>
                                        <input id="postcode" v-model.trim="form.postcode" type="text"
                                            pattern="[0-9A-Za-z]{5}" maxlength="5" class="form-control" required>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="city" class="form-label">Ville</label>
                                        <input id="city" v-model.trim="form.city" type="text" minlength="3"
                                            maxlength="50" class="form-control" required>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                                    <button type="submit" class="btn btn-lg greenButton rounded-pill"
                                        :disabled="saving">
                                        {{ saving ? "Enregistrement..." : "Enregistrer les modifications" }}
                                    </button>
                                    <router-link :to="backPath" class="btn btn-lg btn-outline-secondary rounded-pill">
                                        Annuler
                                    </router-link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </main>
</template>

<script setup>
import axios from "axios"
import { computed, nextTick, onBeforeUnmount, reactive, ref, watch } from "vue"
import { useRoute, useRouter } from "vue-router"
import { useUserStore } from "../../stores/userStore"
import { usePlacesStore } from "../../stores/placesStore"
import LoadingSpinner from "../utilities/LoadingSpinner.vue"
import ValidationErrors from "../utilities/ValidationErrors.vue"

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const placesStore = usePlacesStore()

const place = ref(null)
const loading = ref(true)
const saving = ref(false)
const loadError = ref("")
const validationErrors = ref(null)
const categories = ref([])
const departments = ref([])
const statuses = ["en attente", "validé", "à modifier", "refusé"]
const locationQuery = ref("")
const locationResults = ref([])
const searchingLocations = ref(false)
const locationSearchError = ref("")
const form = reactive({
    name: "",
    description: "",
    latitude: "",
    longitude: "",
    category_id: "",
    note: "",
    time: "",
    difficulty: "",
    kilometres: "",
    department_id: "",
    adress: "",
    postcode: "",
    city: "",
    status: "",
    comment: ""
})
let searchTimer
let searchController
let skipNextLocationSearch = false

const isAdmin = computed(() => Number(userStore.roleId) === 2 || userStore.role === "admin")
const canEdit = computed(() => Boolean(place.value && userStore.userLoggedIn && (
    isAdmin.value || Number(place.value.user_id) === Number(userStore.id)
)))
const backPath = computed(() => isAdmin.value ? "/backoffice" : "/meslieuxpostes")

watch(locationQuery, query => {
    if (skipNextLocationSearch) {
        skipNextLocationSearch = false
        return
    }

    locationResults.value = []
    locationSearchError.value = ""
    searchingLocations.value = false
    clearTimeout(searchTimer)
    searchController?.abort()

    const searchTerm = query.trim()
    if (searchTerm.length < 3) return

    searchTimer = setTimeout(async () => {
        const controller = new AbortController()
        searchController = controller
        searchingLocations.value = true
        try {
            const response = await axios.get("/api/places/geocode", {
                params: { q: searchTerm },
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

const statusClass = status => {
    if (status === "validé") return "status-approved"
    if (status === "en attente") return "status-pending"
    if (status === "à modifier") return "status-changes"
    if (status === "refusé") return "status-rejected"
    return "status-neutral"
}

const firstValue = value => Array.isArray(value) ? value[0] : value

const locationLabel = feature => {
    const properties = feature.properties ?? {}
    const title = firstValue(properties.name) || properties.toponym || properties.label
    const town = firstValue(properties.city)
    return properties.label || [title, town].filter(Boolean).join(", ")
}

const selectLocation = feature => {
    const properties = feature.properties ?? {}
    const point = feature.geometry?.coordinates
    const postalCode = firstValue(properties.postcode)
    const departmentCode = firstValue(properties.depcode) || postalCode?.slice(0, 2)
    const matchedDepartment = departments.value.find(item => String(item.code) === String(departmentCode))
    const town = firstValue(properties.city)

    if (!Array.isArray(point) || point.length < 2 || !postalCode || !town || !matchedDepartment) {
        locationSearchError.value = "Ce résultat ne contient pas assez d’informations. Choisissez une adresse plus précise."
        return
    }

    const label = locationLabel(feature)
    form.latitude = Number(point[1]).toFixed(7)
    form.longitude = Number(point[0]).toFixed(7)
    form.department_id = matchedDepartment.id
    form.adress = (properties.street || firstValue(properties.name) || properties.toponym || properties.label || label).slice(0, 75)
    form.postcode = postalCode
    form.city = town
    locationQuery.value = label
    locationResults.value = []
    locationSearchError.value = ""
}

const loadPlace = async id => {
    loading.value = true
    loadError.value = ""
    validationErrors.value = null
    place.value = null
    locationResults.value = []
    locationSearchError.value = ""

    try {
        const [placeResponse, categoryResponse, departmentResponse] = await Promise.all([
            axios.get(`/api/places/${id}`),
            placesStore.categories?.length ? Promise.resolve(null) : axios.get("/api/categories"),
            placesStore.departments?.length ? Promise.resolve(null) : axios.get("/api/departments")
        ])
        place.value = placeResponse.data.data
        categories.value = categoryResponse?.data.data ?? placesStore.categories ?? []
        departments.value = departmentResponse?.data.data ?? placesStore.departments ?? []
        if (categoryResponse) placesStore.storeCategories(categories.value)
        if (departmentResponse) placesStore.storeDepartments(departments.value)
        Object.assign(form, {
            name: place.value.name ?? "",
            description: place.value.description ?? "",
            latitude: place.value.latitude ?? "",
            longitude: place.value.longitude ?? "",
            category_id: place.value.category_id ?? place.value.category?.id ?? "",
            note: place.value.note ?? "",
            time: place.value.time ?? "",
            difficulty: place.value.difficulty ?? "famille",
            kilometres: place.value.kilometres ?? "",
            department_id: place.value.department_id ?? place.value.department?.id ?? "",
            adress: place.value.adress ?? "",
            postcode: place.value.postcode ?? "",
            city: place.value.city ?? "",
            status: place.value.status ?? "en attente",
            comment: place.value.comment ?? ""
        })
        skipNextLocationSearch = true
        locationQuery.value = [form.adress, form.postcode, form.city].filter(Boolean).join(", ")
        await nextTick()
        skipNextLocationSearch = false
    } catch (requestError) {
        loadError.value = requestError.response?.data?.message
            || "Impossible de charger ce lieu. Vérifiez votre connexion puis réessayez."
    } finally {
        loading.value = false
    }
}

onBeforeUnmount(() => {
    clearTimeout(searchTimer)
    searchController?.abort()
})

const saveChanges = async () => {
    if (!canEdit.value) return

    saving.value = true
    validationErrors.value = null
    const data = {
        name: form.name,
        description: form.description,
        latitude: form.latitude,
        longitude: form.longitude,
        category_id: form.category_id,
        time: form.time,
        difficulty: form.difficulty,
        kilometres: form.kilometres || null,
        department_id: form.department_id,
        adress: form.adress,
        postcode: form.postcode,
        city: form.city
    }
    if (isAdmin.value) {
        data.note = form.note
        data.status = form.status
        data.comment = form.comment || null
    }

    try {
        const response = await axios.put(`/api/places/${place.value.id}`, data)
        const updatedPlace = response.data.data
        if (Array.isArray(placesStore.places)) {
            const index = placesStore.places.findIndex(item => item.id === updatedPlace.id)
            if (index !== -1) placesStore.places.splice(index, 1, updatedPlace)
        }
        if (Array.isArray(userStore.userPlaces)) {
            const index = userStore.userPlaces.findIndex(item => item.id === updatedPlace.id)
            if (index !== -1) userStore.userPlaces.splice(index, 1, updatedPlace)
        }
        const destination = isAdmin.value ? "backoffice" : "meslieuxpostes"
        await router.push(`/successmessage/${destination}/${encodeURIComponent(response.data.message)}`)
    } catch (requestError) {
        validationErrors.value = requestError.response?.data?.errors || {
            general: [requestError.response?.data?.message || "Impossible d'enregistrer le lieu. Réessayez."]
        }
    } finally {
        saving.value = false
    }
}

watch(() => route.params.id, id => loadPlace(id), { immediate: true })
</script>

<style scoped>
.location-results {
    position: absolute;
    z-index: 10;
    width: 100%;
    max-height: 16rem;
    overflow-y: auto;
}

.status-badge {
    display: inline-block;
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
}

.status-pending { color: #7a5700; background-color: #fff0bf; }
.status-changes { color: #80500a; background-color: #ffe2b8; }
.status-rejected { color: #8e2632; background-color: #f8d7da; }
.status-approved { color: #17613f; background-color: #d6f1e4; }
.status-neutral { color: #435b64; background-color: #e6ecee; }
</style>
