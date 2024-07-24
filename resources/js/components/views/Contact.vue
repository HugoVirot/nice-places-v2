<template>
    <div class="m-3">
        <i class="mx-auto fa-solid fa-pen greenIcon fa-3x"></i>
        <h2 class="mt-2">Contact</h2>
    </div>

    <div class="container-fluid p-3 p-lg-4">

        <ValidationErrors :errors="validationErrors" v-if="validationErrors" />

        <div class="row justify-content-center p-2 p-lg-4">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header text-white mb-3">Un problème, une question ou une suggestion ?</div>

                    <div class="card-body">

                        <form @submit.prevent="sendData">

                            <div class="form-group row m-2">
                                <label for="nom" class="col-md-4 col-form-label text-md-right">nom</label>

                                <div class="col-md-6">
                                    <input v-model="nom" id="nom" type="text" class="form-control" name="nom" required
                                        autocomplete="nom" autofocus>
                                </div>
                            </div>
                            <div class="form-group row m-2">
                                <label for="email" class="col-md-4 col-form-label text-md-right">adresse mail</label>
                                <div class="col-md-6">
                                    <input v-model="email" id="email" type="email" class="form-control" name="email"
                                        required autocomplete="email" autofocus>
                                </div>
                            </div>

                            <div class="form-group row m-2">
                                <label for="message" class="col-md-4 col-form-label text-md-right">votre message</label>
                            </div>

                            <div class="form-group row m-2">
                                <div class="col">
                                    <textarea v-model="message" id="" class="form-control" name="message"
                                        autocomplete="message">
                                        Exprimez - vous !</textarea>
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
    </div>
</template>

<script setup>

import { useUserStore } from "../../stores/userStore"
import ValidationErrors from "../utilities/ValidationErrors.vue"
import { useRouter } from "vue-router"

const userStore = useUserStore()
cons router = useRouter()
const name = ref('')
const email = ref('')
const message = ref('')
const validationErrors = ref('')

defineProps(['place_id'])

const sendData = () => {

    let titreMessage

    if (userStore.id) {
        messageTitle = "Message de " + name.value + " email : " + email.value + " (pseudo : " + userStore.name + ")"
    } else {
        messageTitle = "Message de " + name.value + " - " + email.value + " (utilisateur non inscrit ou non connecté)"
    }

    axios.post('/api/notifications', { titre: messageTitle, message: message.value, place_id: null, user_id: 1 })
        .then((response) => {
            router.push('/successmessage/home/' + response.data.message);
        }).catch((error) => {
            validationErrors.value = error.response.data.errors;
        })
}
</script>

<style scoped>
.container-fluid {
    background-image: url(../../../../public/images/starry_sky.jpg);
    background-position: center;
    background-size: cover;
}
</style>