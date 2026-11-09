<template>
    <main v-if="!isAdmin" class="container py-5 text-center" role="alert">
        <h1>Accès réservé à l'administration</h1>
    </main>

    <main v-else class="container-fluid p-3 p-lg-5">
        <header class="p-3 text-center">
            <i class="greenIcon fa-solid fa-3x fa-wrench"></i>
            <h1 class="mt-2">Back-office</h1>
        </header>

        <p v-if="error" class="alert alert-danger" role="alert">{{ error }}</p>
        <p v-if="success" class="alert alert-success" role="status">{{ success }}</p>
        <LoadingSpinner v-if="loading" />

        <template v-else>
            <div class="row g-3 text-center mb-4">
                <button v-for="panel in panels" :key="panel.key" type="button" class="col btn border p-3 mx-2"
                    :class="{ 'border-warning text-warning': panel.pending > 0, 'border-secondary': !panel.pending }"
                    @click="activePanel = panel.key">
                    <i :class="['fa-2x', 'fa-solid', panel.icon, panel.pending ? 'text-warning' : 'greenIcon']"></i>
                    <span class="d-block mt-2">{{ panel.label }}</span>
                    <strong v-if="panel.pending">{{ panel.pending }} à valider</strong>
                </button>
            </div>

            <nav class="nav nav-pills justify-content-center mb-4" aria-label="Sections du back-office">
                <button v-for="panel in panels" :key="panel.key" class="nav-link"
                    :class="{ active: activePanel === panel.key }" @click="activePanel = panel.key">
                    {{ panel.label }}
                </button>
            </nav>

            <section v-if="activePanel === 'categories'">
                <h2>Gestion des catégories</h2>
                <div class="table-responsive">
                    <table class="table align-middle admin-table">
                        <thead><tr><th>Catégorie</th><th>Créée le</th><th>Actions</th></tr></thead>
                        <tbody>
                            <template v-for="category in categories" :key="category.id">
                                <tr>
                                    <td><span class="me-2" :style="{ color: category.color }" v-html="category.icon"></span>{{ category.name }}</td>
                                    <td>{{ formatDate(category.created_at) }}</td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-link p-0 me-3" @click="editCategory(category)" aria-label="Modifier la catégorie"><i class="fa-solid fa-pen-to-square greenIcon"></i></button>
                                        <button class="btn btn-link text-danger p-0" @click="removeItem('categories', category)" aria-label="Supprimer la catégorie"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr v-if="editingCategory?.id === category.id">
                                    <td colspan="3">
                                        <form class="row g-2 align-items-end editor-row" @submit.prevent="saveCategory">
                                            <div class="col-md-4"><label class="form-label">Nom</label><input v-model="editingCategory.name" class="form-control" required minlength="5" maxlength="50"></div>
                                            <div class="col-md-4"><label class="form-label">Icône Font Awesome</label><input v-model="editingCategory.icon" class="form-control" required maxlength="50"></div>
                                            <div class="col-md-2"><label class="form-label">Couleur</label><input v-model="editingCategory.color" class="form-control" required maxlength="15"></div>
                                            <div class="col-md-2"><button class="btn greenButton me-2" :disabled="saving">Enregistrer</button><button type="button" class="btn btn-outline-secondary" @click="editingCategory = null">Annuler</button></div>
                                            <div class="col-md-5"><label class="form-label">Image (JPG, 5 Mo max.)</label><input :ref="element => categoryImageInputs[category.id] = element" type="file" accept=".jpg,.jpeg,image/jpeg" class="form-control"></div>
                                            <div class="col-md-2"><button type="button" class="btn blueButton" :disabled="saving" @click="uploadSelectedCategoryImage(category)">Changer l’image</button></div>
                                            <div class="col-md-5"><img :src="editingCategory.image_url" :alt="`Image de ${editingCategory.name}`" class="category-preview"></div>
                                        </form>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="!categories.length"><td colspan="3">Aucune catégorie.</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="mt-4">Ajouter une catégorie</h3>
                <form class="row g-2 align-items-end" @submit.prevent="createCategory">
                    <div class="col-md-4"><label class="form-label" for="categoryName">Nom</label><input id="categoryName" v-model="newCategory.name" class="form-control" required minlength="5" maxlength="50"></div>
                    <div class="col-md-4"><label class="form-label" for="categoryIcon">Icône Font Awesome</label><input id="categoryIcon" v-model="newCategory.icon" class="form-control" required maxlength="50"></div>
                    <div class="col-md-3"><label class="form-label" for="categoryColor">Couleur</label><input id="categoryColor" v-model="newCategory.color" class="form-control" required maxlength="15"></div>
                    <div class="col-md-2"><button class="btn greenButton" type="submit" :disabled="saving">Ajouter</button></div>
                    <div class="col-md-4"><label class="form-label" for="newCategoryImage">Image JPG (facultative)</label><input id="newCategoryImage" type="file" accept=".jpg,.jpeg,image/jpeg" class="form-control" @change="newCategoryImage = $event.target.files?.[0] ?? null"></div>
                </form>
            </section>

            <section v-else-if="activePanel === 'places'">
                <h2>Gestion des lieux et lieux proposés</h2>
                <div class="table-responsive">
                    <table class="table align-middle admin-table">
                        <thead><tr><th>Lieu</th><th>Statut</th><th>Note</th><th>Ville</th><th>Auteur</th><th>Actions</th></tr></thead>
                        <tbody>
                            <template v-for="place in sortedPlaces" :key="place.id">
                                <tr>
                                    <td><router-link :to="`/lieu/${place.id}`">{{ place.name }}</router-link></td>
                                    <td><StatusBadge :status="place.status" /></td><td>{{ place.note }}</td>
                                    <td>{{ place.city }} ({{ place.postcode }})</td><td>{{ place.user?.name || 'Compte supprimé' }}</td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-link p-0 me-3" @click="editPlace(place)" aria-label="Modifier le lieu"><i class="fa-solid fa-pen-to-square greenIcon"></i></button>
                                        <button class="btn btn-link text-danger p-0" @click="removeItem('places', place)" aria-label="Supprimer le lieu"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="!places.length"><td colspan="6">Aucun lieu.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section v-else-if="activePanel === 'reviews'">
                <h2>Gestion des avis proposés</h2>
                <div class="table-responsive">
                    <table class="table align-middle admin-table">
                        <thead><tr><th>Lieu</th><th>Auteur</th><th>Note</th><th>Commentaire</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            <template v-for="review in sortedReviews" :key="review.id">
                                <tr>
                                    <td>{{ review.place?.name || 'Lieu supprimé' }}</td><td>{{ review.user?.name || 'Compte supprimé' }}</td>
                                    <td>{{ review.note }}/10</td><td>{{ review.comment || '—' }}</td><td><StatusBadge :status="review.status" /></td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-link p-0 me-3" @click="editReview(review)" aria-label="Modifier l'avis"><i class="fa-solid fa-pen-to-square greenIcon"></i></button>
                                        <button class="btn btn-link text-danger p-0" @click="removeItem('reviews', review)" aria-label="Supprimer l'avis"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr v-if="editingReview?.id === review.id">
                                    <td colspan="6">
                                        <form class="row g-2 align-items-end editor-row" @submit.prevent="saveReview">
                                            <div class="col-md-2"><label class="form-label">Note</label><input v-model.number="editingReview.note" type="number" min="1" max="10" class="form-control" required></div>
                                            <div class="col-md-3"><label class="form-label">Statut</label><select v-model="editingReview.status" class="form-select"><option v-for="status in reviewStatuses" :key="status" :value="status">{{ status }}</option></select></div>
                                            <div class="col-md-5"><label class="form-label">Commentaire</label><input v-model="editingReview.comment" class="form-control" maxlength="2000"></div>
                                            <div class="col-md-2"><button class="btn greenButton me-1" :disabled="saving">Enregistrer</button><button type="button" class="btn btn-outline-secondary" @click="editingReview = null">Annuler</button></div>
                                        </form>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="!reviews.length"><td colspan="6">Aucun avis.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section v-else-if="activePanel === 'images'">
                <h2>Gestion des images et images proposées</h2>
                <p v-if="imageSuccess" class="alert alert-success" role="status">{{ imageSuccess }}</p>
                <div class="table-responsive">
                    <table class="table align-middle admin-table">
                        <thead><tr><th>Aperçu</th><th>Lieu</th><th>Auteur</th><th>Statut</th><th>Couverture</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr v-for="image in sortedImages" :key="image.id">
                                <td>
                                    <button type="button" class="image-preview-button"
                                        :aria-label="`Afficher ${image.name} en grand`" @click="previewImage(image)">
                                        <img :src="`/images/${image.name}`" :alt="image.name" class="admin-image">
                                    </button>
                                </td>
                                <td>{{ image.place?.name || '—' }}</td><td>{{ image.user?.name || 'Compte supprimé' }}</td>
                                <td><StatusBadge :status="image.status" /></td>
                                <td><input v-model="image.cover" type="checkbox" class="form-check-input" :aria-label="`Image de couverture ${image.name}`"></td>
                                <td class="text-nowrap">
                                    <select v-model="image.status" class="form-select form-select-sm d-inline-block status-select" :aria-label="`Statut ${image.name}`"><option v-for="status in imageStatuses" :key="status" :value="status">{{ status }}</option></select>
                                    <button class="btn btn-link p-0 mx-2" @click="saveImage(image)" aria-label="Enregistrer l'image"><i class="fa-solid fa-floppy-disk greenIcon"></i></button>
                                    <button class="btn btn-link text-danger p-0" @click="removeItem('images', image)" aria-label="Supprimer l'image"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr v-if="!images.length"><td colspan="6">Aucune image.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section v-else-if="activePanel === 'users'">
                <h2>Gestion des utilisateurs</h2>
                <div class="table-responsive">
                    <table class="table align-middle admin-table">
                        <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Inscription</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.name }}</td><td>{{ user.email }}</td><td>{{ user.role?.name }}</td>
                                <td>{{ formatDate(user.created_at) }}</td>
                                <td><button v-if="Number(user.id) !== Number(userStore.id)" class="btn btn-link text-danger p-0" @click="removeItem('users', user)" aria-label="Supprimer l'utilisateur"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                            <tr v-if="!users.length"><td colspan="5">Aucun utilisateur.</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </template>

        <div v-if="previewedImage" class="image-modal-backdrop" role="presentation" @click.self="closeImagePreview">
            <section class="image-modal" role="dialog" aria-modal="true"
                :aria-label="`Aperçu de ${previewedImage.name}`">
                <button type="button" class="image-modal-close" aria-label="Fermer l’aperçu"
                    @click="closeImagePreview">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
                <img :src="`/images/${previewedImage.name}`" :alt="previewedImage.name" class="image-modal-picture">
                <button type="button" class="btn btn-light rounded-pill" @click="closeImagePreview">Fermer</button>
            </section>
        </div>
    </main>
</template>

<script setup>
import axios from "axios"
import { computed, defineComponent, h, onMounted, reactive, ref } from "vue"
import { useRouter } from "vue-router"
import { useUserStore } from "../../stores/userStore"
import { usePlacesStore } from "../../stores/placesStore"
import LoadingSpinner from "../utilities/LoadingSpinner.vue"

const StatusBadge = defineComponent({
    props: { status: { type: String, required: true } },
    setup(props) {
        const statusClass = computed(() => {
            const value = props.status.toLocaleLowerCase("fr")
            if (value.includes("attente")) return "status-pending"
            if (value.includes("modifier")) return "status-changes"
            if (value.includes("refus")) return "status-rejected"
            if (value.includes("valid")) return "status-approved"
            return "status-neutral"
        })
        return () => h("span", { class: ["status-badge", statusClass.value] }, [
            h("span", { class: "status-dot", "aria-hidden": "true" }),
            props.status
        ])
    }
})

const userStore = useUserStore()
const placesStore = usePlacesStore()
const router = useRouter()
const isAdmin = computed(() => Number(userStore.roleId) === 2 || (!userStore.roleId && userStore.role === "admin"))
const loading = ref(true)
const saving = ref(false)
const error = ref("")
const success = ref("")
const imageSuccess = ref("")
const activePanel = ref("places")
const places = ref([])
const categories = ref([])
const reviews = ref([])
const users = ref([])
const images = ref([])
const editingCategory = ref(null)
const editingReview = ref(null)
const previewedImage = ref(null)
const categoryImageInputs = ref({})
const newCategoryImage = ref(null)
const newCategory = reactive({ name: "", icon: "", color: "" })
const reviewStatuses = ["en attente", "validé", "refusé"]
const imageStatuses = ["en attente", "validée", "refusée"]

const sortPendingFirstById = (items, pendingStatuses) => [...items].sort((a, b) => {
    const aIsPending = pendingStatuses.includes(a.status)
    const bIsPending = pendingStatuses.includes(b.status)

    if (aIsPending !== bIsPending) return aIsPending ? -1 : 1
    return Number(a.id) - Number(b.id)
})

const sortedPlaces = computed(() => sortPendingFirstById(places.value, ["en attente", "à modifier"]))
const sortedReviews = computed(() => sortPendingFirstById(reviews.value, ["en attente"]))
const sortedImages = computed(() => sortPendingFirstById(images.value, ["en attente"]))

const previewImage = image => { previewedImage.value = image }
const closeImagePreview = () => { previewedImage.value = null }

const panels = computed(() => [
    { key: "categories", label: "Catégories", icon: "fa-rectangle-list", pending: 0 },
    { key: "places", label: "Lieux", icon: "fa-location-dot", pending: places.value.filter(place => place.status === "en attente" || place.status === "à modifier").length },
    { key: "reviews", label: "Avis", icon: "fa-pen", pending: reviews.value.filter(review => review.status === "en attente").length },
    { key: "images", label: "Images", icon: "fa-camera", pending: images.value.filter(image => image.status === "en attente").length },
    { key: "users", label: "Utilisateurs", icon: "fa-user", pending: 0 }
])

const loadData = async () => {
    try {
        const [placeResponse, categoryResponse, reviewResponse, userResponse, imageResponse] = await Promise.all([
            axios.get("/api/getallplaces"),
            axios.get("/api/categories"),
            axios.get("/api/reviews"),
            axios.get("/api/users"),
            axios.get("/api/images")
        ])
        places.value = placeResponse.data.data
        categories.value = categoryResponse.data.data
        placesStore.storeCategories(categories.value)
        reviews.value = reviewResponse.data.data
        users.value = userResponse.data.data
        images.value = imageResponse.data.data
    } catch (requestError) {
        error.value = requestError.response?.status === 403
            ? "Votre compte ne dispose pas des droits nécessaires pour consulter le back-office."
            : "Impossible de charger les données du back-office. Vérifiez votre connexion et vos droits d'accès."
    } finally {
        loading.value = false
    }
}

const formatDate = value => value ? new Date(value).toLocaleDateString("fr-FR") : "—"
const clearFeedback = () => { error.value = ""; success.value = "" }
const showRequestError = (requestError, fallback) => {
    error.value = requestError.response?.data?.errors
        ? Object.values(requestError.response.data.errors).flat().join(" ")
        : requestError.response?.data?.message || fallback
}

const editCategory = category => {
    clearFeedback()
    editingCategory.value = { ...category }
}
const saveCategory = async () => {
    saving.value = true
    clearFeedback()
    try {
        const response = await axios.put(`/api/categories/${editingCategory.value.id}`, editingCategory.value)
        const index = categories.value.findIndex(category => category.id === response.data.data.id)
        categories.value.splice(index, 1, response.data.data)
        placesStore.storeCategories(categories.value)
        editingCategory.value = null
        success.value = response.data.message
    } catch (requestError) {
        showRequestError(requestError, "Impossible de modifier la catégorie.")
    } finally {
        saving.value = false
    }
}

const uploadCategoryImage = async (category, file) => {
    if (!file) {
        clearFeedback()
        error.value = "Choisissez une image JPG avant de l’envoyer."
        return false
    }

    saving.value = true
    clearFeedback()
    const formData = new FormData()
    formData.append("image", file)
    try {
        const response = await axios.post(`/api/categories/${category.id}/image`, formData)
        const index = categories.value.findIndex(entry => entry.id === category.id)
        if (index !== -1) categories.value.splice(index, 1, response.data.data)
        placesStore.storeCategories(categories.value)
        if (editingCategory.value?.id === category.id) editingCategory.value.image_url = response.data.data.image_url
        success.value = response.data.message
        return true
    } catch (requestError) {
        showRequestError(requestError, "Impossible de modifier l’image de la catégorie.")
        return false
    } finally {
        saving.value = false
    }
}

const uploadSelectedCategoryImage = async category => {
    const input = categoryImageInputs.value[category.id]
    if (await uploadCategoryImage(category, input?.files?.[0]) && input) input.value = ""
}

const editPlace = place => router.push(`/modifierlieu/${place.id}`)

const editReview = review => {
    clearFeedback()
    editingReview.value = { ...review }
}
const saveReview = async () => {
    saving.value = true
    clearFeedback()
    try {
        const response = await axios.put(`/api/reviews/${editingReview.value.id}`, editingReview.value)
        const index = reviews.value.findIndex(review => review.id === response.data.data.id)
        reviews.value.splice(index, 1, response.data.data)
        editingReview.value = null
        success.value = response.data.message
    } catch (requestError) {
        showRequestError(requestError, "Impossible d'enregistrer l'avis.")
    } finally {
        saving.value = false
    }
}

const saveImage = async image => {
    clearFeedback()
    imageSuccess.value = ""
    saving.value = true
    try {
        const response = await axios.put(`/api/images/${image.id}`, { cover: image.cover, status: image.status })
        const index = images.value.findIndex(entry => entry.id === image.id)
        const updatedImage = response.data.data
        if (index !== -1) images.value.splice(index, 1, updatedImage)
        if (updatedImage.status === "validée") {
            imageSuccess.value = "Image validée avec succès."
        } else if (updatedImage.status === "refusée") {
            imageSuccess.value = "Image refusée avec succès."
        } else {
            imageSuccess.value = response.data.message
        }
    } catch (requestError) {
        showRequestError(requestError, "Impossible d'enregistrer l'image.")
    } finally {
        saving.value = false
    }
}

const removeItem = async (resource, item) => {
    const labels = { places: "ce lieu", categories: "cette catégorie et ses lieux", reviews: "cet avis", users: "cet utilisateur", images: "cette image" }
    if (!window.confirm(`Supprimer ${labels[resource]} ?`)) return

    clearFeedback()
    try {
        await axios.delete(`/api/${resource}/${item.id}`)
        const collections = { places, categories, reviews, users, images }
        collections[resource].value = collections[resource].value.filter(entry => entry.id !== item.id)
        if (resource === "categories") placesStore.storeCategories(categories.value)
        success.value = "Élément supprimé."
    } catch (requestError) {
        showRequestError(requestError, "Impossible de supprimer cet élément.")
    }
}

const createCategory = async () => {
    saving.value = true
    clearFeedback()
    try {
        const imageFile = newCategoryImage.value
        const response = await axios.post("/api/categories", { ...newCategory })
        categories.value.push(response.data.data)
        placesStore.storeCategories(categories.value)
        Object.assign(newCategory, { name: "", icon: "", color: "" })
        newCategoryImage.value = null
        const imageInput = document.getElementById("newCategoryImage")
        if (imageInput) imageInput.value = ""
        if (imageFile) {
            const imageSaved = await uploadCategoryImage(response.data.data, imageFile)
            if (!imageSaved) {
                success.value = "Catégorie créée; son image n’a pas pu être enregistrée."
                return
            }
        } else {
            success.value = response.data.message
        }
    } catch (requestError) {
        showRequestError(requestError, "Impossible de créer la catégorie.")
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    if (isAdmin.value) loadData()
    else loading.value = false
})
</script>

<style scoped>
.admin-table {
    --bs-table-striped-bg: transparent;
    --bs-table-bg: #fff;
    border-color: #b7d9de;
}

.admin-table tbody tr:nth-child(even) > * {
    background-color: #e8f3f4;
}

.admin-table tbody tr:hover > * {
    background-color: #d6ebed;
}

.editor-row {
    padding: 1rem;
    border-left: 4px solid #94d1be;
    background: #f0f8f6;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
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

.admin-image {
    max-width: 100px;
    max-height: 70px;
    object-fit: contain;
}

.image-preview-button {
    padding: 0;
    border: 0;
    background: transparent;
    cursor: zoom-in;
}

.image-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 2050;
    display: grid;
    place-items: center;
    padding: 1.5rem;
    background: rgb(0 0 0 / 75%);
}

.image-modal {
    position: relative;
    display: flex;
    width: min(100%, 1100px);
    max-height: 100%;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 0.75rem;
    background: #fff;
    box-shadow: 0 1rem 3rem rgb(0 0 0 / 35%);
}

.image-modal-picture {
    display: block;
    width: 100%;
    max-height: calc(100vh - 8rem);
    object-fit: contain;
}

.image-modal-close {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    z-index: 1;
    display: grid;
    width: 2.5rem;
    height: 2.5rem;
    place-items: center;
    border: 0;
    border-radius: 50%;
    background: #fff;
    color: #1c6e8c;
    font-size: 1.5rem;
}

.category-preview {
    max-width: 100%;
    max-height: 120px;
    object-fit: cover;
    border-radius: 0.25rem;
}

.status-select {
    max-width: 130px;
}
</style>
