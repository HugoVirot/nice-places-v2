<template>
    <p class="text-secondary mt-3">Afficher les lieux : </p>

    <select required v-model="filtre" class="form-select w-50 mx-auto" aria-label="filtre">
        <option value="france">de la France entière</option>
        <!-- disponible uniquement si le département de l'utilisateur a déjà été renseigné -->
        <option v-if="departement" value="departementUtilisateur">de votre département</option>
        <option v-if="departement" value="regionUtilisateur">de votre région</option>
        <option value="autreDepartement">d'un autre département</option>
        <option value="autreRegion">d'une autre région</option>
    </select>

    <div v-if="filtre == 'autreDepartement'">
        <label class="m-3 text-secondary" for="departmentSelect">Choisissez un département</label>
        <select id="departmentSelect" required v-model="departementFiltre" class="form-select w-50 mx-auto"
            aria-label="filtre">
            <option v-for="(departement, index) in departements" :selected="index == 0" :value="departement.code">{{
            departement.code
            }} - {{ departement.nom }}</option>
        </select>
    </div>

    <div v-if="filtre == 'autreRegion'">
        <label class="m-3 text-secondary" for="regionSelect">Choisissez une région</label>
        <select id="regionSelect" required v-model="regionFiltre" class="form-select w-50 mx-auto" aria-label="filtre">
            <option v-for="(region, index) in regions" :selected="index == 0" :value="region.nom">{{ region.nom }}
            </option>
        </select>
    </div>
</template>  

<script>
import { useLieuxStore } from "../../stores/lieuxStore";
import { useUserStore } from "../../stores/userStore.js";
import { mapState } from "pinia";

export default {
    data() {
        return {
            departementFiltre: '',
            regionFiltre: '',
            filtre: "france",
        }
    },

    computed: {
        ...mapState(useUserStore, ['departement']),
        ...mapState(useLieuxStore, ['departements', 'regions'])
    },

    props: ["lieux", "lieuxNonFiltres"],

    emits: ['filtre_applique'],

    watch: {

        // on surveille le filtre. Si changement, on exécute le code suivant
        filtre(newFilter) {

            // si choix du département de l'utilisateur : on filtre les lieux ainsi
            if (newFilter == "departementUtilisateur") {
                // on filtre les lieux non filtrés et on stocke le résultat dans une variable (impossible de modifier directement les props)
                let lieuxFiltres = this.lieuxNonFiltres.filter(lieu => lieu.departement.code == this.departement.code)
                // on fait remonter les lieux filtrés vers le parent 
                this.$emit("filtre_applique", lieuxFiltres)
            }

            // si choix de la région de l'utilisateur : on filtre les lieux ainsi
            else if (newFilter == "regionUtilisateur") {
                // on filtre les lieux non filtrés et on stocke le résultat dans une variable (impossible de modifier directement les props)
                let lieuxFiltres = this.lieuxNonFiltres.filter(lieu => lieu.departement.region.nom == this.departement.region.nom)
                // on fait remonter les lieux filtrés vers le parent 
                this.$emit("filtre_applique", lieuxFiltres)

            } else if (newFilter == "france") {
                // si choix France entière = on remet les lieux du parent à l'état initial
                this.$emit("filtre_applique", this.lieuxNonFiltres)
            }
        },

        // on surveille le choix du filtre par département (et donc le choix d'un département à cet effet)
        departementFiltre(newDepartement) {

            // on filtre les lieux non filtrés et on stocke le résultat dans une variable (impossible de modifier directement les props)
            let lieuxFiltres = this.lieuxNonFiltres.filter(lieu => lieu.departement.code == newDepartement)
            // on fait remonter les lieux filtrés vers le parent 
            this.$emit("filtre_applique", lieuxFiltres)
        },

        // même principe avec les régions
        regionFiltre(newRegion) {

            let lieuxFiltres = this.lieuxNonFiltres.filter(lieu => lieu.departement.region.nom == newRegion)
            this.$emit("filtre_applique", lieuxFiltres)
        }
    }
}
</script>