<template>

    <div class="p-3">
        <i class="greenIcon mx-auto fa-2x fa-solid fa-user"></i>
        <h1 class="mt-2">Mes lieux favoris</h1>
    </div>

    <section v-if="listeFavoris && listeFavoris.length > 0">

        <Filtres :lieux="listeFavoris" :lieuxNonFiltres="favorisNonFiltres" @filtre_applique="updateLieux" />

        <Tris :lieux="listeFavoris" @lieux_tries="updateLieux" />

        <div class="container-fluid p-3 p-lg-5">

            <div class="row">
                <div class="col-lg-6 border border-3 border-white" v-for="favori in listeFavoris" :key="favori.id">
                   <RectangleLieu :lieu="favori"/>
                </div>
            </div>
        </div>
    </section>

    <section v-else class="p-5">
        <i class="greenIcon fa-solid fa-xmark fa-5x"></i>
        <p>Vous n'avez aucun lieu dans vos favoris.</p>
    </section>

</template>

<script>
import { useUserStore } from "../../stores/userStore"
import { mapActions, mapState } from "pinia"
import Filtres from "../utilities/Filtres.vue"
import Tris from "../utilities/Tris.vue"
import RectangleLieu from "../utilities/RectangleLieu.vue"

export default {
    data() {
        return {
            listeFavoris: [],
            favorisNonFiltres: []
        }
    },

    computed: {
        ...mapState(useUserStore, ['id', 'favoris'])
    },

    components: { Filtres, Tris, RectangleLieu },

    methods: {
        ...mapActions(useUserStore, ['storeFavoris']),

        // récupérer les favoris de l'utilisateur
        getFavoris() {
            axios.get('/api/favoris/' + this.id)
                .then(response => {
                    this.storeFavoris(response.data.data);
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        },

        updateLieux(lieuxTries) {  // déclenchée si filtre appliqué via composant enfant Filtres
            this.listeFavoris = lieuxTries // on remplace les lieux de la catégorie par les lieux filtrés 
        }

    },
    created() {
        // on récupère les lieux postés par l'utilisateur
        this.getFavoris()

        // on les stocke dans la variable listeFavoris pour pouvoir les trier (impossible de modifier une propriété computed / lecture seule)
        this.listeFavoris = this.favoris

        // on stocke les favoris dans une autre variable pour conserver la liste de base en cas de tri
        this.favorisNonFiltres = this.favoris
    }
}
</script>