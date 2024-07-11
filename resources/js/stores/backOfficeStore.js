import { defineStore } from 'pinia'

export const useBackOfficeStore = defineStore({
    // id requis pour connecter le store aux devtools
    id: 'BackOfficeStore',

    state: () => {
        return {
            avis: "",
            users: "",
            images: ""
        }
    },

    getters: {},

    actions: {
        storeAvis(avis) {
            this.avis = avis
        },

        storeUsers(users) {
            this.users = users
        },

        storeImages(images) {
            this.images = images
        }
    },

    persist: true, // activation du plugin de persistance
})