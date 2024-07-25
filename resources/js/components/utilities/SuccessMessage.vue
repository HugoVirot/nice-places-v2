<template>

    <div class="container-fluid greenBackground text-center py-5 my-5 text-white">
        <h3 class="p-2">Félicitations</h3>
        <i class="fa-solid fa-circle-check fa-5x p-2"></i>
        <h2 class="p-2">{{ message !== 'undefined' ? message : 'succès' }}</h2>
    </div>

</template>

<script setup>
import { useRouter } from "vue-router"
import { useRoute } from "vue-router";
import { onMounted } from 'vue';

const router = useRouter()
const route = useRoute()
const nextPage = "/" + route.params.nextpage
const message = route.params.message
const placeId = "/" + route.params.placeId

onMounted(() => {
    // selon le choix fait page précédente, on redirige sur page précédente / accueil / autre page
    setTimeout(() => {
        if (nextPage == "/lastpage") {
            router.go('-1')
        }
        else if (nextPage == "/home") {
            router.push('/')
        }
        else if (nextPage == '/uploadimages') {
            let route = '/uploadimages/' + placeId
            router.push(route)
        } else {
            router.push(nextPage)
        }
    }, 2500)
})
</script>

<style scoped>
h2 {
    color: white
}
</style>