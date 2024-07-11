<template>

    <div v-if="categorie" class="container-fluid p-5"
        :style="`background-image: url(/images/categorie${categorie.id}.jpg); background-position: center; background-size: cover;`">

        <div class="m-auto">
            <span class="fa-5x iconWithShadow" v-html="categorie.icone" :style="{color: categorie.couleur}"></span>
            <h1 class="textWithShadow bigFontSize2 text-white">{{ categorie.nom }}</h1>
        </div>

    </div>

    <Tris :lieux="categorie.lieux" @lieux_tries="updateLieux" />

    <Filtres :lieux="categorie.lieux" :lieuxNonFiltres="lieuxNonFiltres" @filtre_applique="updateLieux" />

    <div class="container-fluid p-3 p-lg-5 mt-3">

        <div v-if="!this.loading">
            <p v-if="categorie.lieux.length > 0" class="pb-2 fs-3">{{ categorie.lieux.length }} lieu(x) trouvé(s)</p>
            <p v-else class="pb-2 fs-3">aucun lieu trouvé</p>
        </div>

        <div class="row">
            <!-- le composant RectangleLieu affiche chaque lieu (qu'il reçoit en props)-->
            <div class="col-lg-6 border border-3 border-white" v-for="lieu in categorie.lieux" :key="lieu.id">
                <RectangleLieu :lieu="lieu" />
            </div>
        </div>
    </div>

</template>

<script>
import axios from "axios";
import Filtres from "../utilities/Filtres.vue"
import Tris from "../utilities/Tris.vue"
import RectangleLieu from "../utilities/RectangleLieu.vue";

export default {
    data() {
        return {
            categorie: '',
            loading: true,
            lieuxNonFiltres: ''
        }
    },

    components: { Filtres, Tris, RectangleLieu },

    methods: {
        updateLieux(lieuxTriesOuFiltres) {  // déclenchée si tri ou filtre appliqué via composant enfant Tris ou  Filtres
            this.categorie.lieux = lieuxTriesOuFiltres // on remplace les lieux de la catégorie par les lieux triés ou filtrés 
        },

        // filtre les lieux en ne gardant que ceux validés
        getValidatedPlaces() {
            this.categorie.lieux = this.categorie.lieux.filter(lieu => lieu.statut == "validé")
        }
    },

    // on récupère la catégorie en fonction de l'id passé en paramètre de la route
    created() {
        axios.get("/api/categories/" + this.$route.params.id)
            .then(response => {
                this.categorie = response.data
                this.getValidatedPlaces()
                this.loading = false
                this.lieuxNonFiltres = this.categorie.lieux
            }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
            })
    }
}
</script>