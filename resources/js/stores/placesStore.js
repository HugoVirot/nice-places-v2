import { defineStore } from 'pinia'

export const usePlacesStore = defineStore({
    // id requis pour connecter le store aux devtools
    id: 'LieuxStore',

    state: () => {
        return {
            places: "",
            categories: "",
            departments: "",
            regions: "",
            threeTopPlaces: "",
            threeLastPlaces: "",
        }
    },

    getters: {
        // on récupère les places validés uniquement
        getValidatedPlaces() {
            return this.places.filter(place => place.statut == "validé")
        },
        //on récupère les 100 places les mieux notés
        getTopRatedPlaces() {
            return this.places.slice(0, 100).sort((a, b) => {
                if (a.note > b.note) return -1;
                return a.note < b.note ? 1 : 0;
            });
        }
    },

    actions: {
        storePlaces(places) {
            this.places = places
        },

        storeDepartements(departments) {
            this.departments = departments
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

        storeNewPlace(newPlace) {
            this.places.push(newPlace)
        }
    },

    persist: true, // activation du plugin de persistance
})