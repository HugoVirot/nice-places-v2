<template>

    <section class="p-2" v-if="userStore.notifications">
        <div class="p-3">
            <i class="greenIcon mx-auto fa-3x fa-solid fa-message"></i>
            <h1 class="mt-2">Mes notifications</h1>
        </div>

        <div class="container">
            <div class="row p-2">
                <div v-if="userStore.notifications.length > 0"
                    class="greenIcon mx-auto my-5 border border-4 rounded border-secondary col-md-4 offset-md-1 py-5">
                    <i class="fa-5x fa-solid fa-envelope mb-3"></i>
                    <p><span class="fs-1">{{ userStore.notifications.length }}</span> notification(s) reçue(s) au total</p>
                </div>

                <div v-show="userStore.notifications.length > 0 && userStore.countUnreadNotifications > 0"
                    class="mx-auto my-5 text-danger border border-4 rounded border-danger col-md-4 offset-md-1 py-5">
                    <i class="fa-5x fa-solid fa-bell mb-3"></i>
                    <p><span class="fs-1">{{ userStore.countUnreadNotifications }}</span> non lues </p>
                </div>
            </div>
        </div>

        <template v-for="notification in userStore.notifications" :key="notification.id">
            <div class="container my-2">
                <div class="accordion" :id="`notification${notification.id}`">
                    <div class="accordion-item">
                        <div class="accordion-header text-white">
                            <button :id="`heading${notification.id}`" class="accordion-button mx-auto row" type="button" data-bs-toggle="collapse"
                                :data-bs-target="`#message${notification.id}`" aria-expanded="false"
                                :aria-controls="`message${notification.id}`"
                                @click="markAsReadWhenOpened(notification, $event)">
                                <p class="text-white col-lg-3">reçue le {{ notification.created_at?.substring(0, 10) }}</p>
                                <h3 class="col-lg-7">{{ notification.title }}</h3>
                                <div v-if="!notification.read" class="text-white col-lg-2">
                                    <i class="fa-solid fa-2x fa-circle-exclamation text-danger me-2"></i>Non lue
                                </div>
                            </button>
                        </div>

                        <div :id="`message${notification.id}`" class="accordion-collapse collapse"
                            :aria-labelledby="`heading${notification.id}`"
                            :data-bs-parent="`#notification${notification.id}`">
                            <div class="accordion-body">
                                <p class="text-center notificationMessage p-2" v-html="notification.message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div v-if="!userStore.notifications.length">
            <p>Vous n'avez aucune notification.</p>
        </div>
    </section>
</template>

<script setup>
import axios from "axios"
import { useUserStore } from "../../stores/userStore"
import { onBeforeMount, ref } from "vue";

const userStore = useUserStore()
const markingNotificationIds = ref(new Set())

// récupérer les notifications de l'utilisateur en fonction de son id
const getNotifications = () => {
    axios.get('/api/getnotificationsbyuser/' + userStore.id)
        .then(response => {
            userStore.storeNotifications(response.data);
        }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        })
}

const markAsReadWhenOpened = notification => {
    if (notification.read || markingNotificationIds.value.has(notification.id)) return

    markingNotificationIds.value.add(notification.id)
    axios.put(`/api/notifications/${notification.id}`)
        .then(response => {
            notification.read = response.data.data.read
        }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
            alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
        }).finally(() => {
            markingNotificationIds.value.delete(notification.id)
        })
}

onBeforeMount(() => {
    // on récupère les notifications de l'utilisateur
    getNotifications()
})

</script>

<style scoped>
h3 {
    color: white
}

.accordion-button {
    background-color: #1C6E8C;
}

.notificationMessage {
    line-height: 3em;
    color: grey
}
</style>