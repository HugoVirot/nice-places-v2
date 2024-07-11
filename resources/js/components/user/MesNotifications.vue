<template>

    <section class="p-2" v-if="notifications">
        <div class="p-3">
            <i class="greenIcon mx-auto fa-3x fa-solid fa-message"></i>
            <h1 class="mt-2">Mes notifications</h1>
        </div>

        <div class="container">
            <div class="row p-2">
                <div v-if="notifications.length > 0"
                    class="greenIcon mx-auto my-5 border border-4 rounded border-secondary col-md-4 offset-md-1 py-5">
                    <i class="fa-5x fa-solid fa-envelope mb-3"></i>
                    <p><span class="fs-1">{{ notifications.length }}</span> notification(s) reçue(s) au total</p>
                </div>

                <div v-show="notifications.length > 0 && countUnreadNotifications > 0"
                    class="mx-auto my-5 text-danger border border-4 rounded border-danger col-md-4 offset-md-1 py-5">
                    <i class="fa-5x fa-solid fa-bell mb-3"></i>
                    <p><span class="fs-1">{{ countUnreadNotifications }}</span> non lues </p>
                </div>
            </div>
        </div>

        <div v-if="showNotificationReadMessage">
            <p class="text-white greenBackground rounded mx-auto w-25 p-3">Notification marquée comme lue</p>
            <button class="btn greenButton" @click="showNotificationReadMessage = false">OK</button>
        </div>

        <div class="container my-2" v-if="notifications.length > 0" v-for="notification in notifications">


            <!-- <p class="text-dark">{{ moment(notification.created_at).format("ddd DD MMM YYYY [à]HH:mm") }}</p> -->

            <div class="accordion" :id="`notification${notification.id}`">
                <div class="accordion-item">

                    <div class="accordion-header text-white" id="headingOne">
                        <button class="accordion-button mx-auto row" type="button" data-bs-toggle="collapse"
                            :data-bs-target="`#message${notification.id}`" aria-expanded="false"
                            :aria-controls="`message${notification.id}`">

                            <p class="text-white col-lg-3">reçue le {{ notification.created_at.substring(0, 10)}}</p>

                            <h3 class="col-lg-7">{{ notification.titre }}</h3>

                            <div v-if="!notification.lue" class="text-white col-lg-2"><i
                                    class="fa-solid fa-2x fa-circle-exclamation text-danger me-2"></i>Non lue</div>
                        </button>
                    </div>

                    <div :id="`message${notification.id}`" class="accordion-collapse collapse"
                        aria-labelledby="headingOne" :data-bs-parent="`#notification${notification.id}`">
                        <div class="accordion-body">
                            <p class="text-center notificationMessage p-2" v-html="notification.message"></p>
                            <button v-if="!notification.lue" class="btn greenButton mx-auto"
                                @click="markNotificationAsRead(notification.id)">OK (marquer comme lue)</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div v-else>
            <p>Vous n'avez aucune notification.</p>
        </div>
    </section>
</template>

<script>
import { useUserStore } from "../../stores/userStore"
import { mapActions } from "pinia";
import { mapState } from "pinia";

export default {
    computed: {
        ...mapState(useUserStore, ['id', 'notifications', 'countUnreadNotifications']),
    },

    data() {
        return {
            showFullMessage: false,
            showNotificationReadMessage: false
        }
    },

    methods: {
        // on rend accessible l'action storeNotifications du userStore
        ...mapActions(useUserStore, ['storeNotifications']),

        // récupérer les notifications de l'utilisateur en fonction de son id
        getNotifications() {
            axios.get('/api/getnotificationsbyuser/' + this.id)
                .then(response => {
                    this.storeNotifications(response.data);
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        // marquer une notification comme lue
        markNotificationAsRead(notificationId) {
            axios.put('/api/notifications/' + notificationId)
                .then(() => {
                    this.showNotificationReadMessage = true
                    this.getNotifications()
                    this.$router.push('/mesnotifications')
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    },

    created() {
        // on récupère les notifications de l'utilisateur
        this.getNotifications()
    }
}
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