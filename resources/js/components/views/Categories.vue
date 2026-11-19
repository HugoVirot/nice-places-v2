
<template>
    <div class="p-5">
        <i class="greenIcon fa-solid fa-3x fa-book-atlas"></i>
        <h1 class="mt-2">Catégories</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">
        <p v-if="loadError" class="alert alert-warning" role="status">{{ loadError }}</p>
        <div class="row">
            <router-link v-for="category in categories" :key="category.id"
                :to="`/categories/${category.id}`"
                class="category-image-overlay category-image-card col-lg-6 border border-3 border-white card card35vh text-white text-decoration-none"
                :aria-label="`Voir la catégorie ${category.name}`">
                <img class="category-image" :src="category.image_url || `/images/category${category.id}.jpg`"
                    alt="" aria-hidden="true">
                <span class="category-image-title m-auto">
                    <span class="category-name p-3 textWithShadow bigFontSize">{{ category.name }}</span>
                </span>
            </router-link>
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

<style scoped>
.category-image-card {
    position: relative;
    overflow: hidden;
    isolation: isolate;
}

.category-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: width 250ms ease, height 250ms ease, top 250ms ease, left 250ms ease;
}

.category-image-title {
    position: relative;
    z-index: 1;
}

.category-name {
    position: relative;
    display: inline-block;
    color: #fff !important;
}

.category-name::after {
    position: absolute;
    right: 0.75rem;
    bottom: 0.4rem;
    left: 0.75rem;
    height: 3px;
    background: #94d1be;
    content: "";
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 200ms ease;
}

.category-image-card:hover .category-name::after,
.category-image-card:focus-visible .category-name::after {
    transform: scaleX(1);
}

.category-image-card:hover .category-image,
.category-image-card:focus-visible .category-image {
    top: -5%;
    left: -5%;
    width: 110%;
    height: 110%;
}

@media (prefers-reduced-motion: reduce) {
    .category-image,
    .category-name::after {
        transition: none;
    }
}
</style>
