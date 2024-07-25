import { defineStore } from 'pinia'
import axios from 'axios'

export const useUserStore = defineStore({
    // id requis pour connecter le store aux devtools
    id: 'UserStore',

    state: () => {
        return {
            name: "",
            email: "",
            id: "",
            department: "",
            token: "",
            role: "",
            userLoggedIn: false,
            geolocationAnswered: false,
            userPosition: "",
            userPlaces: "",
            favorites: "",
            notifications: ""
        }
    },

    getters: {
        // retourne le nombre de notifications non lues
        countUnreadNotifications() {
            if (this.notifications) {
                return this.notifications.filter(n => !n.read).length
            }
            else {
                return null
            }
        }

    },

    actions: { // stocker les infos de l'utilisateur dans le store
        // appelée lors de la connexion et lors de la modif des infos
        storeUserData(userData) {
            this.name = userData.name
            this.email = userData.email
            this.id = userData.id
            this.department = userData.department
            this.region = userData.department ? userData.department.region.nom : null
            this.role = userData.role.name

            //on définit le statut de l'utilisateur : il est connecté
            this.userLoggedIn = true
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

        storeFavorites(favorites) {
            this.favorites = favorites
        }
    },

    persist: true, // activation du plugin de persistance
})