import { defineStore } from 'pinia'
import axios from 'axios'

export const useUserStore = defineStore({
    // id requis pour connecter le store aux devtools
    id: 'UserStore',

    state: () => {
        return {
            pseudo: "",
            email: "",
            id: "",
            departement: "",
            token: "",
            role: "",
            userLoggedIn: false,
            geolocationAnswered: false,
            userPosition: "",
            userPlaces: "",
            favoris: "",
            notifications: ""
        }
    },

    getters: {
        // retourne le nombre de notifications non lues
        countUnreadNotifications() {
            if (this.notifications) {
                return this.notifications.filter(n => !n.lue).length
            }
            else {
                return null
            }
        }

    },

    actions: { // stocker les infos de l'utilisateur dans le store
        // appelée lors de la connexion et lors de la modif des infos
        storeUserData(userData) {
            this.pseudo = userData.pseudo
            this.email = userData.email
            this.id = userData.id
            this.departement = userData.departement
            this.region = userData.departement ? userData.departement.region.nom : null
            this.role = userData.role

            // si token présent dans userData (= connexion, pas présent si modif infos)
            if (userData.token) {
                // on stocke le token dans le store
                this.token = userData.token
                // pour transmettre le token (créé par l'API) avec chaque requête si connecté
                axios.defaults.headers.common.Authorization = `Bearer ${userData.token}`
                // on définit le statut de l'utilisateur : il est connecté
                this.userLoggedIn = true
            }
        },

        // mémoriser le fait qu'un choix a été fait par rapport à la géoloc
        storeGeolocationAnswered(answer) {
            this.geolocationAnswered = answer
        },

        // stocker la position de l'utilisateur si géoloc acceptée
        storeUserPosition(userCoordinates) {
            this.userPosition = userCoordinates
        },

        storeNotifications(notifications) {
            this.notifications = notifications
        },

        storeUserPlaces(userPlaces) {
            this.userPlaces = userPlaces
        },

        storeFavoris(favoris) {
            this.favoris = favoris
        }
    },

    persist: true, // activation du plugin de persistance
})