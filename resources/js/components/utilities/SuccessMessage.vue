<template>
    <div class="success-viewport">
        <Transition appear name="success">
            <section v-if="visible" class="success-message greenBackground text-center text-white">
                <h3 class="p-2">Félicitations</h3>
                <i class="fa-solid fa-circle-check fa-5x p-2" aria-hidden="true"></i>
                <h2 class="p-2">{{ message !== 'undefined' ? message : 'succès' }}</h2>
            </section>
        </Transition>
    </div>
</template>

<script setup>
import { useRouter } from "vue-router"
import { useRoute } from "vue-router"
import { onBeforeUnmount, onMounted, ref } from "vue"

const router = useRouter()
const route = useRoute()
const nextPage = "/" + route.params.nextpage
const message = route.params.message
const placeId = route.params.lieuid
const visible = ref(true)
let exitTimer
let redirectTimer

onMounted(() => {
    // selon le choix fait page précédente, on redirige sur page précédente / accueil / autre page
    exitTimer = setTimeout(() => {
        visible.value = false
    }, 2150)

    redirectTimer = setTimeout(() => {
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

onBeforeUnmount(() => {
    clearTimeout(exitTimer)
    clearTimeout(redirectTimer)
})
</script>

<style scoped>
.success-viewport {
    min-height: max(0px, calc(100vh - 13rem));
    display: grid;
    place-items: center;
    padding: 2rem 0;
}

.success-message {
    width: 50%;
    margin: 0 auto;
    padding: 3rem 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 1rem 2.5rem rgb(28 110 140 / 18%);
    transform-origin: center;
}

h2 {
    color: white;
    overflow-wrap: anywhere;
}

.success-enter-active,
.success-leave-active {
    transition: opacity 350ms ease, transform 350ms ease;
}

.success-enter-from,
.success-leave-to {
    opacity: 0;
    transform: translateY(1.25rem) scale(0.97);
}

@media (max-width: 767.98px) {
    .success-message {
        width: 75%;
        padding: 2rem 1rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .success-enter-active,
    .success-leave-active {
        transition-duration: 1ms;
    }
}
</style>