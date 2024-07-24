<template>
    <div class="container-fluid p-3 p-lg-4">

        <div class="m-3">
            <i class="mx-auto fa-solid fa-pen greenIcon fa-3x"></i>
            <h2 class="mt-2">Poster un avis</h2>
        </div>

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div v-if="id" class="row justify-content-center p-2 p-lg-4">
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
                                <label for="comment" class="col-md-4 col-form-label text-md-right"></label>

                                <div class="col-md-6">
                                    <textarea v-model="comment" id="comment" class="form-control"
                                        name="comment" autocomplete="comment">
                                        comment (facultatif)</textarea>
                                </div>
                            </div>

                            <div class="form-group row mt-3 text-center">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit"
                                        class="greenButton btn btn-lg rounded-pill text-light btn-info">
                                        Valider
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
import { useUserStore } from "../../stores/userStore"
import ValidationErrors from "./ValidationErrors.vue"

const note = ref('')
const comment = ref('')
const validationErrors = ref('')

defineProps(['place_id'])

const sendData = () => {
    axios.post('/api/avis', { note: note.value, comment: comment.value, place_id: place_id.value, user_id: userStore.id })
        .then((response) => {
            this.$router.push('/successmessage/lastpage/' + response.data.message);
        }).catch((error) => {
            this.validationErrors = error.response.data.errors;
        })
}
</script>