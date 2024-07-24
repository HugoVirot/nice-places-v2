
<template>
    <div class="p-5">
        <i class="greenIcon fa-solid fa-3x fa-book-atlas"></i>
        <h1 class="mt-2">Catégories</h1>
    </div>

    <div class="container-fluid p-3 p-lg-5">
        <div class="row">
            <div class="col-lg-6 border border-3 border-white card card35vh text-white" v-for="category in placesStore.categories"
                :key="category.id"
                :style="`background-image: url(images/category${category.id}.jpg); background-position: center; background-size: cover;`">

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
import { mapState } from "pinia";
import { usePlacesStore } from "../../stores/placesStore.js";

const placesStore = usePlacesStore()
</script>
