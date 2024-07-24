<script setup>
import Header from "./template/Header.vue"
import Slider from "./utilities/Slider.vue"
import Map from "./utilities/Map.vue"
import Footer from "./template/Footer.vue"
import { useUserStore } from '../stores/userStore'
import { placesStore } from '../stores/placesStore'
import axios from "axios"
import { onBeforeMount, onMounted } from "vue"

const userStore = useUserStore()
const placesStore = usePlacesStore()

// on surveille le choix d'un département ou le changement de département de l'utilisateur
// si c'est le cas => on récupère les 3 derniers places et les 3 mieux notés du nouveau département
watch(userStore.department, () => getThreeTopAndLastPlaces())

// on surveille également le changement d'id et donc d'utilisateur
watch(userStore.id, () => getThreeTopAndLastPlaces())

// et les lieux (permet de récupérer les 3 derniers lieux / 3 top lieux au 1er chargement de App)
watch(placesStore.places, () => getThreeTopAndLastPlaces())


// on récupère les catégories et on les stocke dans le store, idem ensuite pour places/départements/régions/favoris
const getCategories = () => {
	axios.get("/api/categories")
		.then(response => {
			placesStore.storeCategories(response.data.data)
		}).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
			alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
		})
}

const getPlaces = () => {
	axios.get("/api/places")
		.then(response => {
			placesStore.storeLieux(response.data.data)
		}).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
			alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
		})
}

const getDepartments = () => {
	axios.get("/api/departments")
		.then(response => {
			placesStore.storeDepartments(response.data.data)
		}).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
			alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
		})
}

const getRegions = () => {
	axios.get("/api/regions")
		.then(response => {
			placesStore.storeRegions(response.data.data)
		}).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
			alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
		})
}

const getFavorites = () => {
	axios.get("/api/favoris/" + userStore.id)
		.then(response => {
			userStore.storeFavoris(response.data.data)
		}).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
			alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
		})
}

// ***************** on récupère les 3 endroits les mieux notés + les 3 derniers postés ****************
const getThreeTopAndLastPlaces = () => {

	let filteredPlacesList

	// si l'utilisateur a choisi un département
	if (this.department) {
		// on filtre l'ensemble des places pour ne garder que ceux du département de l'utilisateur
		filteredPlacesList = this.places.filter(place => place.department.id == this.department.id)

		//sinon => on cible la France entière
	} else {
		filteredPlacesList = this.places
	}

	// on trie les places par note et on garde les 3 mieux notés, puis on les stocke dans le store
	let threeTopPlaces = filteredPlacesList.sort((a, b) => {
		if (a.note > b.note) return -1;
		return a.note < b.note ? 1 : 0;
	}).slice(0, 3)

	this.storeThreeTopPlaces(threeTopPlaces)

	// idem pour les 3 derniers postés
	let threeLastPlaces = filteredPlacesList.sort((a, b) => {
		if (a.created_at > b.created_at) return -1;
		return a.created_at < b.created_at ? 1 : 0;
	}).slice(0, 3)

	this.storeThreeLastPlaces(threeLastPlaces)
}


onBeforeMount(() => {

	// on récupère les cat / places / dep / régions et on les stocke dans le store
	// getPlaces est nécessaire à chaque affichage du composant pour actualiser la liste
	// (les autres changent rarement surtout départements et régions)
	getPlaces()

	if (!placesStore.categories) {
		getCategories()
	}

	if (!placesStore.departments) {
		getDepartments()
	}

	if (!placesStore.regions) {
		getRegions()
	}
	// on récupère également les favoris de l'utilisateur
	getFavorites()
})


onMounted(() => {

	// ******************* si département choisi (à l'inscription ou après) ************************

	// on récupère les 3 derniers lieux ajoutés + les 3 les mieux notés du dép.
	// on fait cela dans onMounted pour être sûr que les lieux soient disponibles (sinon erreur)
	// par défaut, s'effectue 1 seule fois par session car App est chargé au début (composant principal)
	// le watch plus haut permet de surveiller les éventuels changements de département et d'actualiser ces deux "top 3"
	if (placesStore.places) {
		getThreeTopAndLastPlaces()
	}
})
</script>

<template>
	<div class="text-center">

		<!-- ******************************************* HEADER *********************************************** -->

		<Header />

		<div v-if="$route.path !== '/'">
			<!-- si la route est différente de / (racine du site) -->
			<!-- on affiche le template du composant concerné par la route -->
			<router-view v-bind:key="$route.fullPath"></router-view>
		</div>

		<div v-else>
			<!-- si la route est / => on affiche le code de l'accueil-->

			<!-- ******************************************* SLIDER *********************************************** -->

			<Slider />

			<div id="presentationTopStripe" class="mt-5"></div>

			<div class="container-fluid p-5" id="presentation" style="background-image: url(images/mervent2.jpg)">
				<div class="row pb-4">
					<div class="col-md-6 my-auto">
						<img id="bigLogo" class="w-100 h-100 bg-white rounded" src="images/logo.png"
							alt="logo nice places">
					</div>

					<div class="col-md-6 my-auto">
						<p class="text-white fs-3">Un site et un appli mobile simples à utiliser, pour trouver et
							partager
							des
							sorties nature
							(plage,
							forêt, lac, montagne...) près de chez vous !</p>
					</div>
				</div>

				<router-link to="/inscription"><button class="btn btn-lg greenButton mt-3 rounded-pill">Je
						m'inscris</button>
				</router-link>
			</div>

			<div id="presentationBottomStripe" class="mb-3"></div>

			<i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-map-location-dot"></i>
			<h2 class="fs-2 m-3"> Carte des lieux autour de chez vous</h2>


			<!-- *********************************************** CARTE ************************************************** -->

			<Map />

			<!-- *********************************************** CATEGORIES ************************************************** -->

			<div class="container-fluid">

				<div>
					<i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-book-atlas"></i>
					<h2 class="fs-2 m-3">Catégories</h2>
				</div>

				<div class="row m-1 p-1" id="categories">

					<!-- ********************** boucle qui affiche les 3 catégories avec le + de places ************************* -->

					<div v-for="(category, index) in categories.slice(0, 3)" :key="category.id"
						class="mx-auto col-md-6 col-lg-4 p-5 border border-white" :style="`background-image: url(/images/categorie${category.id}.jpg); 
													background-position: center; background-size: cover;`">

						<p class="fs-2 m-auto text-white textWithShadow p-5">{{ category.name }}</p>

						<router-link :to="`/categories/${category.id}`"><button
								class="btn btn-lg greenButton rounded-pill mt-2">voir les lieux</button>
						</router-link>

					</div>

				</div>

				<router-link to="/categories"><button class="btn btn-lg blueButton mt-3 rounded-pill">Toutes les
						catégories</button>
				</router-link>

			</div>


			<!-- ******************************************* TOP DES LIEUX *********************************************** -->

			<div v-if="threeTopPlaces && threeTopPlaces.length > 2">
				<div>
					<i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-star"></i>
					<h2 v-if="department" class="fs-2 m-3">Le top des lieux dans votre département</h2>
					<h2 v-else class="fs-2 m-3">Le top des lieux (France entière)</h2>
				</div>

				<div class="container-fluid">

					<div class="row m-1 p-1" id="topplaces">

						<!-- ********************** boucle qui affiche les 3 places ************************* -->

						<div v-for="(topPlace, index) in threeTopPlaces"
							class="mx-auto col-md-6 col-lg-4 p-2 border border-white d-flex flex-column justify-content-between"
							:style="`background-image: url(images/${topPlace.cover_image[0] ? topPlace.cover_image[0].name : 'defaultpicture.jpg'
								}); background-position: center; background-size: cover;`">
							<div class="infosTopLieux pt-1 pb-3">

								<div class="rankingAndName row d-flex justify-content-between align-items-center">
									<div class="col-2">
										<span class="ranking"> #{{ index + 1 }} </span>
									</div>
									<div class="col-10">
										<h5>{{ topPlace.name }}</h5>
									</div>
								</div>

								<div class="row">
									<div class="col-6">
										<i class="yellowStar fa-2x fa-solid fa-star me-1"></i> {{ topPlace.note }}
										<span class="fs-4 rounded-circle border border-white p-2 ms-2"
											v-html="topPlace.category.icone"></span>
									</div>
									<div class="col-6 d-flex">
										<i class="greenIcon fa-2x fa-solid fa-location-dot me-2"></i>
										<p class="text-white my-auto">{{ topPlace.city }} ({{
											topPlace.postcode.substr(0, 2)
										}})</p>
									</div>
								</div>

							</div>

							<router-link :to="`/place/${topPlace.id}`"><button
									class="btn btn-lg greenButton rounded-pill">découvrir</button></router-link>

						</div>

					</div>

					<router-link to="/topplaces"><button class="btn btn-lg blueButton mt-3 rounded-pill">Classement
							complet</button>
					</router-link>

				</div>
			</div>


			<!-- ********************************* DERNIERS LIEUX AJOUTES *************************************** -->

			<div v-if="threeLastPlaces && threeLastPlaces.length > 2">

				<i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-clock"></i>
				<h2 v-if="department" class="fs-2 m-3"> Derniers places ajoutés dans votre département</h2>
				<h2 v-else class="fs-2 m-3"> Derniers places ajoutés (France entière)</h2>

				<div class="container-fluid">

					<div class="row m-1 p-1" id="derniersplaces">

						<!-- ********************** boucle qui affiche les 3 derniers places ************************* -->

						<div v-for="lastPlace in threeLastPlaces"
							class="mx-auto col-md-6 col-lg-4 p-2 border border-white d-flex flex-column justify-content-between"
							:style="`background-image: url(images/${lastPlace.image_mise_en_avant[0] ? lastPlace.image_mise_en_avant[0].name : 'defaultpicture.jpg'
								}); background-position: center; background-size: cover;`">
							<div class="infosDerniersLieux pt-2 px-1">

								<div class="row">
									<div class="col-2">
										<span class="fs-4 rounded-circle border border-white p-1 ms-2"
											v-html="lastPlace.category.icone"></span>
									</div>
									<div class="col-10">
										<h5>{{ lastPlace.name }}</h5>
									</div>
								</div>

								<div class="row">
									<div class="row col">

										<div class="col-3">
											<i class="greenIcon fa-2x fa-solid fa-location-dot"></i>
										</div>

										<div class="col-9">
											<p class="text-white">{{ lastPlace.city }} ({{
												lastPlace.postcode.substr(0, 2) }})</p>
										</div>

									</div>

									<div class="row col-md-8" v-if="lastPlace.city.length < 15">

										<div class="col-3">
											<i class="greenIcon fa-2x fa-solid fa-user"></i>
										</div>

										<div class="col-9" v-if="lastPlace.user">
											<p class="text-white">par {{ lastPlace.user.name }}</p>
										</div>
									</div>

								</div>

							</div>

							<router-link :to="`/place/${lastPlace.id}`"><button
									class="btn btn-lg greenButton rounded-pill">découvrir</button></router-link>

						</div>

					</div>
				</div>

				<router-link to="/proposerplace"><button class="btn btn-lg blueButton mt-3 rounded-pill">Proposer un
						lieu</button>
				</router-link>

			</div>

			<!-- *********************************** INSCRIVEZ-VOUS ************************************** -->

			<section id="inscrivezVous" class="mt-2">

				<i class="greenIcon mt-5 mx-auto fa-3x fa-solid fa-user-plus"></i>
				<h2 class="fs-2 m-3">Inscrivez-vous gratuitement !</h2>

				<div class="container-fluid px-5 mt-4">
					<div class="row" id="inscrivezVousIcones">
						<div class="col-md-4 p-1">
							<img class="inscrivezVousIcones" src="images/icons/france.png" alt="France">
							<p class="fs-4 mt-3">Des lieux dans toute la France</p>
						</div>

						<div class="col-md-4 p-1">
							<img id="pointer" src="images/icons/pointer.png" alt="pointeur">
							<p class="fs-4 mt-3">Partagez vos coins préférés</p>
						</div>

						<div class="col-md-4 p-1">
							<img class="inscrivezVousIcones" src="images/icons/phone.png" alt="téléphone">
							<p class="fs-4 mt-3">Un site web et une appli mobile</p>
						</div>
					</div>
				</div>

				<router-link to="/inscription"><button class="btn btn-lg blueButton mt-3 rounded-pill">C'est décidé, je
						m'inscris !</button>
				</router-link>
			</section>

		</div>
	</div>

	<!-- **************************************** FOOTER ******************************************** -->

	<Footer />
</template>

<style>
/******************* REGLES GENERALES ************/

.blueElement {
	color: #1c6e8c
}

.greenBackground {
	background-color: #94D1BE;
}

/******************* titres ************/

h1,
h2 {
	color: #1c6e8c
}

/******************* icônes ************/

.greenIcon {
	color: #94D1BE
}

.inscrivezVousIcones {
	width: 10vw;
	height: 10vw
}

.yellowStar {
	color: yellow !important
}

.categoryicon {
	background-color: white;
}

/******************* images ************/

#bigLogo {
	height: 1.7em
}

/******************* boutons ************/

.greenButton {
	color: white;
	background-color: #94D1BE;
}

.greenButton:hover {
	color: white;
	background-color: #1C6E8C;
}

.blueButton {
	color: white;
	background-color: #1C6E8C;
}

.blueButton:hover {
	color: white;
	background-color: #94D1BE;
}

/************* ombres ************/

.textWithShadow {
	text-shadow: 2px 2px 4px #1C6E8C;
}

.iconWithShadow {
	text-shadow: 2px 2px 4px #fff;
}

/************* Textes et polices ************/

.bigFontSize {
	font-size: 2.5em;
}

.bigFontSize2 {
	margin-top: -4vh;
	font-size: 4em;
}

.blueElement {
	color: #1C6E8C
}

p {
	color: grey
}

/******************* cards ************/

.card {
	color: #1C6E8C;
	background: rgba(254, 254, 254, 0.73)
}

.card-header {
	background-color: #94D1BE
}


.card35vh {
	height: 35vh
}

/******************* classements ************/

.ranking {
	font-size: 2em;
}

.rankingAndName {
	height: 70%
}

/******************* SECTIONS ACCUEIL *****************/

/******************* section présentation ************/

#presentationTopStripe {
	background-color: #94D1BE;
	height: 2vh;
}

#presentationBottomStripe {
	background-color: #1C6E8C;
	height: 2vh;
}

#presentation {
	background-position: center;
	background-size: cover;
	height: 55vh
}

/******************* sections places ************/

#topplaces,
#derniersplaces {
	height: 50vh;
	color: white
}

.infosTopLieux,
.infosDerniersLieux {
	background-color: rgba(50, 61, 158, 0.5);
	height: 34%
}

/******************* inscrivez-vous ************/


#inscrivezVous p {
	color: #1c6e8c;
}

#pointer {
	width: 6vw;
	height: 10vw
}


/********* MEDIA QUERIES (principalement pour l'accueil) ********/

@media screen and (max-width: 380px) {
	#topplaces .rounded-circle {
		display: none;
	}
}

@media screen and (max-width: 580px) {

	#topplaces,
	#derniersplaces {
		height: 120vh;
	}

	#presentation {
		height: 62vh
	}

	.inscrivezVousIcones {
		width: 30vw;
		height: 30vw
	}

	#pointer {
		width: 20vw;
		height: 30vw
	}

	.bigFontSize2 {
		margin-top: -3vh;
		font-size: 3em;
	}
}

@media screen and (min-width: 581px) and (max-width: 768px) {

	#topplaces,
	#derniersplaces {
		height: 85vh;
	}

	#presentation {
		height: 57vh
	}

	.infosTopLieux {
		height: 55%
	}

	.infosDerniersLieux {
		height: 60%
	}
}

@media screen and (min-width: 769px) and (max-width: 992px) {

	#topplaces,
	#derniersplaces {
		height: 60vh;
	}
}

@media screen and (min-width: 581px) and (max-width: 992px) {
	.inscrivezVousIcones {
		width: 17vw;
		height: 17vw
	}

	#pointer {
		width: 11vw;
		height: 17vw
	}
}

@media screen and (max-width: 992px) {

	.infosTopLieux {
		height: 40%
	}

	.infosDerniersLieux {
		height: 45%
	}
}

@media screen and (min-width: 993px) {
	.infosDerniersLieux {
		height: 35%
	}
}

@media screen and (max-width: 768px) {
	.bigFontSize {
		font-size: 2em;
	}
}
</style>