<template>
    <section id="topLieux" class="p-2 text-secondary">
        <div class="p-3">
            <i class="greenIcon mx-auto fa-3x fa-solid fa-message"></i>
            <h1 class="mt-2">Top 100 des lieux</h1>
        </div>

        <Filtres :lieux="topLieux" :lieuxNonFiltres="topLieuxNonFiltres" @filtre_applique="updateLieux" />

        <div class="container-fluid p-3 p-lg-5">

            <div v-if="topLieux" class="row">

                <div v-if="topLieux.length > 0" class="col-lg-6 border border-3 border-white textWithShadow"
                    v-for="(topLieu, index) in topLieux" :key="topLieu.id">
                    <RectangleLieu :lieu="topLieu" :index="index" />
                </div>

                <div v-else>
                    <p>Aucun lieu de ce département dans le top 100.</p>
                </div>

            </div>

            <div v-else>
                <p>chargement en cours...</p>
            </div>

        </div>
    </section>
</template>

<script>
import { mapWritableState } from 'pinia'
import Filtres from "../utilities/Filtres.vue"
import { useLieuxStore } from '../../stores/lieuxStore'
import RectangleLieu from '../utilities/RectangleLieu.vue'

export default {

    computed: {
        ...mapWritableState(useLieuxStore, ['getTopRatedPlaces']),
    },

    data() {
        return {
            topLieux: '',
            topLieuxNonFiltres: ''
        }
    },

    components: { Filtres, RectangleLieu },

    methods: {
        updateLieux(lieuxTries) {  // déclenchée si filtre appliqué via composant enfant Filtres
            this.topLieux = lieuxTries // on remplace le classement affiché par les lieux filtrés 
        }
    },

    created() {
        // on appelle le getter getTopRatedPlaces pour récupérer les 100 lieux les mieux notés
        // on les stocke dans la variable toplieux des datas
        this.topLieux = this.getTopRatedPlaces
        // on garde une copie de cette liste des 100 lieux les mieux notés en cas de retour
        // à l'affichage normal après avoir effectué un ou plusieurs tris
        this.topLieuxNonFiltres = this.topLieux
    }
}
</script>
