<template>
    <div id="carouselAccueil" class="carousel slide carousel-fade container" data-bs-ride="carousel">

        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="images/coulon.jpg" class="d-block w-100" alt="marais poitevin">
                <div class="carousel-caption captionfondbleu d-none d-md-block p-5 mt-auto">
                    <h2>Redécouvrez votre région !</h2>
                    <p>Vous serez étonné(e) par les nombreux lieux extraordinaires qu'elle propose.</p>
                    <router-link to="/carte"><button class="btn btn-lg greenButton">Voir la carte des lieux</button>
                    </router-link>
                </div>
                <h2 class="carousel-caption d-block d-md-none fs-1">Redécouvrez votre région !</h2>
            </div>

            <div class="carousel-item">
                <img src="images/plage-des-chardons.jpg" class="d-block w-100" alt="plage de la Faute">
                <div class="carousel-caption captionfondbleu d-none d-md-block p-5">
                    <h2>Partagez vos coups de coeur !</h2>
                    <p>Faites profiter la communauté de vos petits coins de paradis.</p>

                    <router-link to="/proposerlieu"><button class="btn btn-lg greenButton">Partager un lieu</button>
                    </router-link>
                </div>
                <h2 class="carousel-caption d-block d-md-none fs-1">Partagez vos coups de coeur</h2>
            </div>

            <div class="carousel-item">
                <img src="images/mervent.jpg" class="d-block w-100" alt="forêt de Mervent">
                <div class="carousel-caption captionfondbleu d-none d-md-block p-5">
                    <h2>Destination nature !</h2>
                    <p>Il y a toujours une nouvelle sortie sur Nice Places !
                    </p>
                    <router-link to="/categories"><button class="btn btn-lg greenButton">Explorer les catégories</button>
                    </router-link>
                </div>
                <h2 class="carousel-caption d-block d-md-none fs-1 ">Destination nature !</h2>
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

<script>
export default {
}
</script>

<style scoped>
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
</style>