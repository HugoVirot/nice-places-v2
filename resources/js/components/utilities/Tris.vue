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

<script>
export default {
    data() {
        return {
            tri: '',
            sortedByRating: false, // par défaut : lieux non triés par note,
            sortedByTime: false,
            sortedByDifficulty: false
        }
    },

    props: ['lieux'],

    emits: ['lieux_tries'],

    methods: {
        // tri par note moyenne
        sortByRating() {
            // on stocke les lieux dans une variable temporaire (impossible de modifier une prop)
            let lieuxATrier = this.lieux

            if (!this.sortedByRating) { // si lieux pas encore triés par note : tri décroissant

                lieuxATrier.sort((a, b) => {
                    if (a.note > b.note) return -1;
                    return a.note < b.note ? 1 : 0;
                });

            } else {

                lieuxATrier.sort((a, b) => {
                    if (a.note < b.note) return -1;
                    return a.note > b.note ? 1 : 0;
                });
            }

            this.sortedByRating = !this.sortedByRating

            this.$emit("lieux_tries", lieuxATrier)
        },

        // tri par temps moyen sur place
        sortByTime() {
            // on stocke les lieux dans une variable temporaire (impossible de modifier une prop)
            let lieuxATrier = this.lieux

            if (!this.sortedByTime) { // si lieux pas encore triés par temps : tri croissant (+ court au + long)

                lieuxATrier.sort((a, b) => {
                    if (a.temps < b.temps) return -1;
                    return a.temps > b.temps ? 1 : 0;
                });

            } else { // si lieux déjà triés par temps : tri décroissant (+ long au - long)

                lieuxATrier.sort((a, b) => {
                    if (a.temps > b.temps) return -1;
                    return a.temps < b.temps ? 1 : 0;
                });
            }

            this.sortedByTime = !this.sortedByTime

            this.$emit("lieux_tries", lieuxATrier)
        },

        // tri par difficulté
        sortByDifficulty() {
            // on stocke les lieux dans une variable temporaire (impossible de modifier une prop)
            let lieuxATrier = this.lieux
            let lieuxTries = []

            let easyPlaces = []
            let mediumPlaces = []
            let hardPlaces = []

            // on trie les lieux en 3 tableaux selon leur difficulté
            lieuxATrier.forEach(lieu => {
                switch (lieu.difficulte) {
                    case ('famille'):
                        easyPlaces.push(lieu)
                        break
                    case ('amateur'):
                        mediumPlaces.push(lieu)
                        break
                    case ('sportif'):
                        hardPlaces.push(lieu)
                        break
                }
            })

            // selon l'état actuel du tri, on concatène les tableaux dans un ordre ou dans l'autre
            if (!this.sortedByDifficulty) {
                lieuxTries = easyPlaces.concat(mediumPlaces).concat(hardPlaces)

            } else {
                lieuxTries = hardPlaces.concat(mediumPlaces).concat(easyPlaces)

            }

            this.sortedByDifficulty = !this.sortedByDifficulty
            
            this.$emit("lieux_tries", lieuxTries)
        },
    }
}
</script>