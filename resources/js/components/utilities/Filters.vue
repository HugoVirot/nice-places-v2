<template>
    <div class="place-filters">
        <p class="text-secondary mt-3">Afficher les lieux :</p>

        <select v-model="filter" class="form-select w-50 mx-auto" aria-label="Filtrer les lieux">
            <option value="france">de la France entière</option>
            <option v-if="userStore.department" value="userDepartment">de votre département</option>
            <option v-if="userStore.department" value="userRegion">de votre région</option>
            <option value="otherDepartment">d'un autre département</option>
            <option value="otherRegion">d'une autre région</option>
        </select>

        <div v-if="filter === 'otherDepartment'">
            <label class="m-3 text-secondary" for="departmentSelect">Choisissez un département</label>
            <select id="departmentSelect" v-model="departmentFilter" class="form-select w-50 mx-auto"
                aria-label="Département">
                <option v-for="department in placesStore.departments || []" :key="department.id"
                    :value="department.id">
                    {{ department.code }} - {{ department.name }}
                </option>
            </select>
        </div>

        <div v-if="filter === 'otherRegion'">
            <label class="m-3 text-secondary" for="regionSelect">Choisissez une région</label>
            <select id="regionSelect" v-model="regionFilter" class="form-select w-50 mx-auto"
                aria-label="Région">
                <option v-for="region in placesStore.regions || []" :key="region.id" :value="region.id">
                    {{ region.name }}
                </option>
            </select>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue"
import { usePlacesStore } from "../../stores/placesStore"
import { useUserStore } from "../../stores/userStore.js"

const props = defineProps({
    places: { type: Array, default: () => [] },
    unfilteredPlaces: { type: Array, default: () => [] }
})

const emit = defineEmits(["filterApplied"])
const placesStore = usePlacesStore()
const userStore = useUserStore()

const departmentFilter = ref("")
const regionFilter = ref("")
const filter = ref("france")

const applyFilter = () => {
    let filteredPlaces = props.unfilteredPlaces

    if (filter.value === "userDepartment" && userStore.department) {
        filteredPlaces = props.unfilteredPlaces.filter(
            place => place.department?.id === userStore.department.id
        )
    } else if (filter.value === "userRegion" && userStore.department?.region) {
        filteredPlaces = props.unfilteredPlaces.filter(
            place => place.department?.region?.id === userStore.department.region.id
        )
    } else if (filter.value === "otherDepartment" && departmentFilter.value) {
        filteredPlaces = props.unfilteredPlaces.filter(
            place => place.department?.id === Number(departmentFilter.value)
        )
    } else if (filter.value === "otherRegion" && regionFilter.value) {
        filteredPlaces = props.unfilteredPlaces.filter(
            place => place.department?.region?.id === Number(regionFilter.value)
        )
    }

    emit("filterApplied", filteredPlaces)
}

watch([filter, departmentFilter, regionFilter], applyFilter)
</script>
