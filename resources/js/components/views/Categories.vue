
<template>
    <div class="p-5">
        <i class="greenIcon fa-solid fa-3x fa-book-atlas"></i>
        <h1 class="mt-2">Catégories</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">
        <p v-if="loadError" class="alert alert-warning" role="status">{{ loadError }}</p>
        <div class="row">
            <div class="category-image-overlay col-lg-6 border border-3 border-white card card35vh text-white" v-for="category in categories"
                :key="category.id"
                :style="{ backgroundImage: `url('${category.image_url || `/images/category${category.id}.jpg`}')`, backgroundPosition: 'center', backgroundSize: 'cover' }">

                <span class="m-auto">
                    <div class="p-3 textWithShadow bigFontSize"> {{ category.name }} </div>
                    <router-link :to="`/categories/${category.id}`"><button class="btn greenButton btn-lg">Voir</button>
                    </router-link>
                </span>

            </div>
        </div>
    </div>

</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import { usePlacesStore } from "../../stores/placesStore.js";

const placesStore = usePlacesStore()
const categories = ref(placesStore.categories ?? [])
const loadError = ref("")

onMounted(async () => {
    try {
        const response = await axios.get("/api/categories")
        categories.value = response.data.data
        placesStore.storeCategories(categories.value)
    } catch {
        loadError.value = categories.value.length
            ? "Impossible d’actualiser les catégories; la liste affichée peut être obsolète."
            : "Impossible de charger les catégories. Rechargez la page pour réessayer."
    }
})
</script>
