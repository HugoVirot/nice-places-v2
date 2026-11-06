<template>

    <div class="pt-5 pb-2">
        <i class="mx-auto greenIcon fa-3x fa-solid fa-paper-plane"></i>
        <h1 class="mt-2">Proposer une image</h1>
        <h2>{{ place.name }}</h2>
    </div>

    <div class="container my-5" v-if="place.images && place.images.length > 0">
        <p class="mb-3">actuellement {{ place.images.length }} image(s) pour ce place</p>

        <div class="row my-2" v-if="place.images.length == 1">
            <div v-for="image in place.images" class="col-md-10 offset-md-1 my-1">
                <img class="previousPictures" :src="`/images/${image.name}`" :alt="`${image.name}`">
            </div>
        </div>

        <div class="row my-2" v-else>
            <div v-for="image in place.images" class="col-md-6 my-1">
                <img class="previousPictures" :src="`/images/${image.name}`" :alt="`${image.name}`">
            </div>
        </div>

    </div>

    <div class="container-fluid p-3 p-lg-5">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div class="row justify-content-center p-2 p-lg-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-white mb-3">Partagez votre plus belle photo !</div>

                    <div class="card-body">
                        <form @submit.prevent="sendData" enctype="multipart/form-data">

                            <div class="form-group row mx-2 mt-3">
                                <input type="file" accept="image/jpeg,image/png" class="form-control-file col-md-8 offset-md-2"
                                    v-on:change="onChange">
                            </div>

                            <div class="form-group row mt-4 py-3 bg-white">
                                <div class="form-text mb-3 col-md-8 offset-md-2">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <i class="my-1 fa-3x fa-solid greenIcon fa-weight-scale"></i>
                                            <p>Moins de 2 Mo</p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <i class="my-1 fa-3x fa-solid greenIcon fa-rectangle-list"></i>
                                            <p>Formats acceptés : JPG / JPEG et PNG</p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <i class="my-1 fa-3x fa-solid greenIcon fa-panorama"></i>
                                            <p>Uniquement en format paysage svp</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <p class="form-text">Vous pourrez ajouter une image plus tard; l’image par défaut sera utilisée entre-temps.</p>
                                    <button type="submit" class="btn btn-lg greenButton rounded-pill" :disabled="submitting || !selectedImage">
                                        {{ submitting ? "Envoi en cours…" : "Valider" }}
                                    </button>
                                    <button type="button" class="btn btn-lg blueButton rounded-pill text-light ms-2"
                                        :disabled="submitting" @click="skipImage">
                                        Plus tard
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import axios from "axios";
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useUserStore } from "../../stores/userStore";
import { useRoute, useRouter } from "vue-router";
import { onBeforeMount, ref } from "vue";

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const placeId = route.params.id
const place = ref({})
const selectedImage = ref(null)
const validationErrors = ref(null)
const submitting = ref(false)

const onChange = e => {
    selectedImage.value = e.target.files?.[0] ?? null
}

const skipImage = () => {
    router.push("/meslieuxpostes")
}

const sendData = async () => {
    if (!selectedImage.value) {
        validationErrors.value = { image: ["Sélectionnez une image avant de l'envoyer."] }
        return
    }

    submitting.value = true
    validationErrors.value = null
    const formData = new FormData()
    formData.append("image", selectedImage.value)
    formData.append("place_id", placeId)

    try {
        const response = await axios.post("/api/images", formData)
        await router.push(`/successmessage/home/${encodeURIComponent(response.data.message)}`)
    } catch (requestError) {
        validationErrors.value = requestError.response?.data?.errors || {
            image: [requestError.response?.data?.message || "Impossible d'envoyer l'image. Réessayez."]
        }
    } finally {
        submitting.value = false
    }
}

onBeforeMount(() => {
    axios.get("/api/places/" + placeId)
        .then(response => {
            place.value = response.data.data
        })
        .catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            validationErrors.value = { general: ["Impossible de récupérer ce lieu. Rechargez la page et réessayez."] }
        })
})
</script>

<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/lake.jpg);
    background-position: center;
    background-size: cover;
}

.previousPictures {
    max-height: 100%;
    max-width: 100%
}
</style>