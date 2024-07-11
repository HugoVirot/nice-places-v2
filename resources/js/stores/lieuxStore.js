import { defineStore } from 'pinia'

export const useLieuxStore = defineStore({
    // id requis pour connecter le store aux devtools
    id: 'LieuxStore',

    state: () => {
        return {
            lieux: "",
            categories: "",
            departements: "",
            regions: "",
            threeTopPlaces: "",
            threeLastPlaces: "",
        }
    },

    getters: {
        // on récupère les lieux validés uniquement
        getValidatedPlaces() {
            return this.lieux.filter(lieu => lieu.statut == "validé")
        },
        //on récupère les 100 lieux les mieux notés
        getTopRatedPlaces() {
            return this.lieux.slice(0, 100).sort((a, b) => {
                if (a.note > b.note) return -1;
                return a.note < b.note ? 1 : 0;
            });
        }
    },

    actions: {
        storeLieux(lieux) {
            this.lieux = lieux
        },

        storeDepartements(departements) {
            this.departements = departements
        },

        storeCategories(categories) {
            this.categories = categories
        },

        storeRegions(regions) {
            this.regions = regions
        },

        storeThreeTopPlaces(threeTopPlaces) {
            this.threeTopPlaces = threeTopPlaces
        },

        storeThreeLastPlaces(threeLastPlaces) {
            this.threeLastPlaces = threeLastPlaces
        },

        storeImages(images) {
            this.images = images
        },

        storeNewLieu(newLieu) {
            this.lieux.push(newLieu)
        }
    },

    persist: true, // activation du plugin de persistance
})