<template>
    <div class="container-fluid p-3 p-lg-4">

        <div class="m-3">
            <i class="mx-auto fa-solid fa-pen greenIcon fa-3x"></i>
            <h2 class="mt-2">Poster un avis</h2>
        </div>

        <ValidationErrors :errors="validationErrors" v-if="Object.keys(validationErrors).length" />
        <p v-if="submitError" class="alert alert-danger" role="alert">{{ submitError }}</p>

        <div v-if="userStore.id" class="row justify-content-center p-2 p-lg-4">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header text-white mb-3">Racontez-nous votre expérience</div>

                    <div class="card-body">

                        <form @submit.prevent="sendData">

                            <div class="form-group row m-2">
                                <label for="note" class="col-md-4 col-form-label text-md-right">note</label>

                                <div class="col-md-6">
                                    <input v-model="note" id="note" type="number" min="1" max="10" class="form-control"
                                        name="note" required autocomplete="note" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="comment" class="col-md-4 col-form-label text-md-right">commentaire (facultatif)</label>

                                <div class="col-md-6">
                                    <textarea v-model="comment" id="comment" class="form-control" name="comment"
                                        autocomplete="comment" maxlength="2000"
                                        placeholder="Racontez-nous votre expérience (facultatif)"></textarea>
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" :disabled="submitting"
                                        class="greenButton btn btn-lg rounded-pill text-light btn-info">
                                        {{ submitting ? "Envoi en cours..." : "Valider" }}
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <p class="text-secondary">Vous devez être connecté pour poster un avis.</p>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from "../../stores/userStore"
import ValidationErrors from "./ValidationErrors.vue"

const userStore = useUserStore()
const router = useRouter()
const props = defineProps(['place_id'])

const note = ref('')
const comment = ref('')
const validationErrors = ref({})
const submitError = ref('')
const submitting = ref(false)

const sendData = async () => {
    if (submitting.value) return

    submitting.value = true
    validationErrors.value = {}
    submitError.value = ''

    try {
        const response = await axios.post('/api/reviews', {
            note: Number(note.value),
            comment: comment.value.trim() || null,
            place_id: props.place_id
        })

        await router.push(`/successmessage/lastpage/${encodeURIComponent(response.data.message)}`)
    } catch (error) {
        if (error.response?.status === 422) {
            validationErrors.value = error.response.data.errors || {}
        } else {
            submitError.value = "Impossible d'enregistrer votre avis. Vérifiez votre connexion puis réessayez."
        }
    } finally {
        submitting.value = false
    }
}
</script>