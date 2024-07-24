<template>
    <p class="text-secondary mt-3">Trier par : </p>

    <div class="container pb-5">
        <button class="btn btn-lg greenButton m-1" v-on:click="sortByRating()">
            Note<i class="fa-solid fa-star ms-2 me-1"></i>
        </button>
        <button class="btn btn-lg m-1 greenButton" v-on:click="sortByTime()">
            Temps moyen sur place <i class="fa-solid fa-hourglass-half ms-2 me-1"></i>
        </button>
        <button class="btn btn-lg m-1 greenButton" v-on:click="sortByDifficulty()">
            Difficulté<i class="fa-solid fa-gauge-simple-high ms-2 me-1"></i>
        </button>
    </div>

</template>

<script setup>

const sortedByRating = ref(false)
const sortedByTime = ref(false)
const sortedByDifficulty = ref(false)

defineProps(['places'])
defineEmits(['placesSorted'])

// tri par note moyenne
const sortByRating = () => {
    // on stocke les lieux dans une variable temporaire (impossible de modifier une prop)
    let placesToBeSorted = this.places

    if (!this.sortedByRating) { // si lieux pas encore triés par note : tri décroissant

        placesToBeSorted.sort((a, b) => {
            if (a.note > b.note) return -1;
            return a.note < b.note ? 1 : 0;
        });

    } else {

        placesToBeSorted.sort((a, b) => {
            if (a.note < b.note) return -1;
            return a.note > b.note ? 1 : 0;
        });
    }

    sortedByRating.value = !sortedByRating.value

    emit("placesSorted", placesToBeSorted)
}

// tri par temps moyen sur place
const sortByTime = () => {
    // on stocke les lieux dans une variable temporaire (impossible de modifier une prop)
    let placesToBeSorted = this.places

    if (!this.sortedByTime) { // si lieux pas encore triés par temps : tri croissant (+ court au + long)

        placesToBeSorted.sort((a, b) => {
            if (a.temps < b.temps) return -1;
            return a.temps > b.temps ? 1 : 0;
        });

    } else { // si lieux déjà triés par temps : tri décroissant (+ long au - long)

        placesToBeSorted.sort((a, b) => {
            if (a.temps > b.temps) return -1;
            return a.temps < b.temps ? 1 : 0;
        });
    }

    sortedByTime.value = sortedByTime.value

    emit("placesSorted", placesToBeSorted)
}

// tri par difficulté
sortByDifficulty() {
    // on stocke les lieux dans une variable temporaire (impossible de modifier une prop)
    let placesToBeSorted = this.places
    let sortedPlaces = []

    let easyPlaces = []
    let mediumPlaces = []
    let hardPlaces = []

    // on trie les lieux en 3 tableaux selon leur difficulté
    placesToBeSorted.forEach(place => {
        switch (place.difficulte) {
            case ('famille'):
                easyPlaces.push(place)
                break
            case ('amateur'):
                mediumPlaces.push(place)
                break
            case ('sportif'):
                hardPlaces.push(place)
                break
        }
    })

    // selon l'état actuel du tri, on concatène les tableaux dans un ordre ou dans l'autre
    if (!sortedByDifficulty) {
        sortedPlaces = easyPlaces.concat(mediumPlaces).concat(hardPlaces)
    } else {
        sortedPlaces = hardPlaces.concat(mediumPlaces).concat(easyPlaces)
    }

    sortedByDifficulty.value = !sortedByDifficulty.value

    emit("placesSorted", sortedPlaces)
}
</script>