<template>
    <router-link :to="`/place/${place.id}`">
        <div class="card card35vh text-white p-2" :style="`background-image: url(/images/${place.cover_image[0] ? place.cover_image[0].nom : 'defaultpicture.jpg'
            }); background-position: center; background-size: cover;`">

            <div class="p-2 textWithShadow d-flex flex-column justify-content-between">
                <div class="fs-3 text-white">{{ place.name }}</div>
                <p class="text-white fs-5">
                    <span class="iconWithShadow p-2 ms-2" :style="{ color: place.category.color }"
                        v-html="place.category.icon"></span>
                    <i class="yellowStar fa-solid fa-star ms-2 me-1"></i>{{ place.note }}
                    <i class="blueElement fa-solid fa-hourglass-half ms-2 me-1"></i>
                    {{ place.time }} heure(s)
                    <i class="greenIcon fa-solid fa-gauge-simple-high ms-2 me-1"></i>
                    {{ place.difficulty }}
                </p>
            </div>

            <div v-if="$route.name && $route.name == 'topplaces'" class="row">
                <div class="col-6 d-flex justify-content-start ms-2 align-items-center">
                    <span class="ranking">#{{ index + 1 }}</span>
                </div>
            </div>

            <div v-if="$route.name && $route.name == 'mesplacespostes'" class="row">

                <div class="col-md-6 mb-1">
                    <div v-if="place.status == 'validé'" class="mx-auto bg-success w-50 rounded">validé</div>
                    <div v-else-if="place.status == 'en attente'" class="mx-auto bg-info w-50 rounded">en attente de
                        validation
                    </div>
                    <div v-else-if="place.status == 'à modifier'" class="mx-auto bg-warning w-50 rounded">à modifier pour
                        être
                        validé
                    </div>
                    <div v-else class="mx-auto bg-danger">refusé
                    </div>
                </div>

                <div class="col-md-6">
                    <router-link :to="`modifierLieu/${place.id}`"><button class="btn blueButton">Modifier place</button>
                    </router-link>
                </div>
            </div>

        </div>
    </router-link>
</template>

<script setup>
defineProps(["place", "index"])
</script>


<style scoped>
.ranking {
    font-size: 4em;
}
</style>