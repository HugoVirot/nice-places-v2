<template>
    <p class="text-secondary mt-3">Afficher les lieux : </p>

    <select required v-model="filter" class="form-select w-50 mx-auto" aria-label="filter">
        <option value="france">de la France entière</option>
        <!-- disponible uniquement si le département de l'utilisateur a déjà été renseigné -->
        <option v-if="department" value="departmentUtilisateur">de votre département</option>
        <option v-if="department" value="regionUtilisateur">de votre région</option>
        <option value="autreDepartement">d'un autre département</option>
        <option value="autreRegion">d'une autre région</option>
    </select>

    <div v-if="filter == 'autreDepartement'">
        <label class="m-3 text-secondary" for="departmentSelect">Choisissez un département</label>
        <select id="departmentSelect" required v-model="departmentFilter" class="form-select w-50 mx-auto"
            aria-label="filtre">
            <option v-for="(department, index) in departments" :selected="index == 0" :value="department.code">{{
                department.code
                }} - {{ department.nom }}</option>
        </select>
    </div>

    <div v-if="filter == 'autreRegion'">
        <label class="m-3 text-secondary" for="regionSelect">Choisissez une région</label>
        <select id="regionSelect" required v-model="regionFilter" class="form-select w-50 mx-auto" aria-label="filtre">
            <option v-for="(region, index) in regions" :selected="index == 0" :value="region.name">{{ region.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { usePlacesStore } from "../../stores/placesStore";
import { useUserStore } from "../../stores/userStore.js";

const userStore = useUserStore()
const placesStore = usePlacesStore()

const departmentFilter = ref('')
const regionFilter = ref('')
const filter = ref('france')

const props = defineProps(["places", "unfilteredPlaces"]);

defineEmits(['filterApplied'])


// on surveille le filtre. Si changement, on exécute le code suivant
watch(filter, async (newFilter, oldFilter) => {

    // si choix du département de l'utilisateur : on filtre les lieux ainsi
    if (newFilter == "departmentUtilisateur") {
        // on filtre les lieux non filtrés et on stocke le résultat dans une variable (impossible de modifier directement les props)
        let filteredPlaces = unfilteredPlaces.filter(lieu => lieu.department.code == department.code)
        // on fait remonter les lieux filtrés vers le parent 
        emit("filterApplied", filteredPlaces)
    }

    // si choix de la région de l'utilisateur : on filtre les lieux ainsi
    else if (newFilter == "regionUtilisateur") {
        // on filtre les lieux non filtrés et on stocke le résultat dans une variable (impossible de modifier directement les props)
        let filteredPlaces = unfilteredPlaces.filter(lieu => lieu.department.region.nom == department.region.nom)
        // on fait remonter les lieux filtrés vers le parent 
        emit("filterApplied", filteredPlaces)

    } else if (newFilter == "france") {
        // si choix France entière = on remet les lieux du parent à l'état initial
        emit("filterApplied", unfilteredPlaces)
    }
})

// on surveille le choix du filtre par département (et donc le choix d'un département à cet effet)
watch (departmentFilter, async (newDepartment, oldDepartment) => {

    // on filtre les lieux non filtrés et on stocke le résultat dans une variable (impossible de modifier directement les props)
    let fiteredPlaces = unfilteredPlaces.filter(place => place.department.code == newDepartment)
    // on fait remonter les lieux filtrés vers le parent 
    emit("filterApplied", fiteredPlaces)
})

// même principe avec les régions
watch (regionFilter, async (newRegion, oldRegion) => {

    let fiteredPlaces = unfilteredPlaces.filter(place => place.department.region.name == newRegion)
    emit("filtre_applique", filteredPlaces)
})

</script>