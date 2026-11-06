<template>
    <div
        id="carouselAccueil"
        class="carousel slide carousel-fade"
        data-bs-ride="carousel"
        :style="{ height: sliderHeight, '--mobile-slide-width': mobileSlideWidth }"
    >

        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="../../../../public/images/coulon.jpg" class="d-block w-100" alt="marais poitevin">
                <div class="carousel-caption captionfondbleu d-none d-md-block p-5 mt-auto">
                    <h2>Redécouvrez votre région !</h2>
                    <p>Vous serez étonné(e) par les nombreux lieux extraordinaires qu'elle propose.</p>
                    <router-link to="/carte"><button class="btn btn-lg greenButton">Voir la carte des lieux</button>
                    </router-link>
                </div>
                <div class="carousel-caption captionfondbleu mobile-caption d-md-none">
                    <h2>Redécouvrez votre région !</h2>
                    <p>Vous serez étonné(e) par les nombreux lieux extraordinaires qu'elle propose.</p>
                    <router-link to="/carte"><button class="btn greenButton">Voir la carte des lieux</button></router-link>
                </div>
            </div>

            <div class="carousel-item">
                <img src="../../../../public/images/plage-des-chardons.jpg" class="d-block w-100" alt="plage de la Faute">
                <div class="carousel-caption captionfondbleu d-none d-md-block p-5">
                    <h2>Partagez vos coups de coeur !</h2>
                    <p>Faites profiter la communauté de vos petits coins de paradis.</p>

                    <router-link to="/proposerlieu"><button class="btn btn-lg greenButton">Partager un lieu</button>
                    </router-link>
                </div>
                <div class="carousel-caption captionfondbleu mobile-caption d-md-none">
                    <h2>Partagez vos coups de coeur !</h2>
                    <p>Faites profiter la communauté de vos petits coins de paradis.</p>
                    <router-link to="/proposerlieu"><button class="btn greenButton">Partager un lieu</button></router-link>
                </div>
            </div>

            <div class="carousel-item">
                <img src="../../../../public/images/mervent.jpg" class="d-block w-100" alt="forêt de Mervent">
                <div class="carousel-caption captionfondbleu d-none d-md-block p-5">
                    <h2>Destination nature !</h2>
                    <p>Il y a toujours une nouvelle sortie sur Nice Places !
                    </p>
                    <router-link to="/categories"><button class="btn btn-lg greenButton">Explorer les catégories</button>
                    </router-link>
                </div>
                <div class="carousel-caption captionfondbleu mobile-caption d-md-none">
                    <h2>Destination nature !</h2>
                    <p>Il y a toujours une nouvelle sortie sur Nice Places !</p>
                    <router-link to="/categories"><button class="btn greenButton">Explorer les catégories</button></router-link>
                </div>
            </div>

            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselAccueil" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselAccueil" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselAccueil" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselAccueil" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselAccueil" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>

    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const sliderHeight = ref('100dvh');
const mobileSlideWidth = ref('75dvh');

const updateSliderSize = () => {
    const headerHeight = document.querySelector('header')?.getBoundingClientRect().height ?? 0;
    const viewportHeight = window.visualViewport?.height ?? window.innerHeight;
    const height = Math.max(viewportHeight - headerHeight, 0);

    sliderHeight.value = `${height}px`;
    mobileSlideWidth.value = `${height * 0.75}px`;
};

onMounted(() => {
    updateSliderSize();
    window.addEventListener('resize', updateSliderSize);
    window.visualViewport?.addEventListener('resize', updateSliderSize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateSliderSize);
    window.visualViewport?.removeEventListener('resize', updateSliderSize);
});
</script>

<style scoped>
#carouselAccueil {
    width: 100%;
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
}

.carousel-inner,
.carousel-item {
    height: 100%;
}

.carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carouselAccueil h2 {
    color: white !important
}

.captionfondbleu {
    background-color: rgba(50, 61, 158, 0.5);
}

p {
    font-size: larger;
    color: white
}

h2 {
    font-size: 3.8vw;
    z-index: 1;
    color: white;
}

@media (max-width: 767.98px) {
    .carousel-item > .mobile-caption {
        top: 5%;
        bottom: auto;
        left: 8%;
        right: 8%;
        width: auto;
        padding: 1rem;
        border-radius: 0.5rem;
        transform: none;
    }

    .mobile-caption h2 {
        margin-bottom: 0.5rem;
        font-size: clamp(2rem, 8vw, 2.75rem);
    }

    .mobile-caption p {
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .mobile-caption .btn {
        font-size: 0.95rem;
    }

    .carousel-item img {
        position: relative;
        left: 50%;
        width: 75vh;
        width: var(--mobile-slide-width, 75dvh);
        max-width: none;
        transform: translateX(-50%);
    }
}
</style>