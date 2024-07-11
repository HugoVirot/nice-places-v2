 <template>
    <router-link :to="`/lieu/${lieu.id}`">
        <div class="card card35vh text-white p-2" :style="`background-image: url(/images/${
        lieu.image_mise_en_avant[0] ?  lieu.image_mise_en_avant[0].nom : 'defaultpicture.jpg'
        }); background-position: center; background-size: cover;`">

            <div class="p-2 textWithShadow d-flex flex-column justify-content-between">
                <div class="fs-3 text-white">{{ lieu.nom }}</div>
                <p class="text-white fs-5">
                    <span class="iconWithShadow p-2 ms-2" :style="{ color: lieu.categorie.couleur }"
                        v-html="lieu.categorie.icone"></span>
                    <i class="yellowStar fa-solid fa-star ms-2 me-1"></i>{{ lieu.note }}
                    <i class="blueElement fa-solid fa-hourglass-half ms-2 me-1"></i>
                    {{ lieu.temps }} heure(s)
                    <i class="greenIcon fa-solid fa-gauge-simple-high ms-2 me-1"></i>
                    {{ lieu.difficulte }}
                </p>
            </div>

            <div v-if="$route.name && $route.name == 'toplieux'" class="row">
                <div class="col-6 d-flex justify-content-start ms-2 align-items-center">
                    <span class="ranking">#{{ index + 1 }}</span>
                </div>
            </div>

            <div v-if="$route.name && $route.name == 'meslieuxpostes'" class="row">

                <div class="col-md-6 mb-1">
                    <div v-if="lieu.statut == 'validé'" class="mx-auto bg-success w-50 rounded">validé</div>
                    <div v-else-if="lieu.statut == 'en attente'" class="mx-auto bg-info w-50 rounded">en attente de
                        validation
                    </div>
                    <div v-else-if="lieu.statut == 'à modifier'" class="mx-auto bg-warning w-50 rounded">à modifier pour
                        être
                        validé
                    </div>
                    <div v-else class="mx-auto bg-danger">refusé
                    </div>
                </div>

                <div class="col-md-6">
                    <router-link :to="`modifierLieu/${lieu.id}`"><button class="btn blueButton">Modifier lieu</button>
                    </router-link>
                </div>
            </div>

        </div>
    </router-link>
</template>

<script>
export default {
    props: ["lieu", "index"]
}
</script>


<style scoped>
.ranking {
    font-size: 4em;
}
</style>