<script setup>
import Header from "./components/template/Header.vue"
import Slider from "./components/utilities/Slider.vue"
import Map from "./components/utilities/Map.vue"
import Footer from "./components/template/Footer.vue"
import { useUserStore } from './stores/userStore'
import { usePlacesStore } from './stores/placesStore'
import axios from "axios"
import { onBeforeMount, watch, ref } from "vue"
  import { storeToRefs } from 'pinia'

const userStore = useUserStore()
const placesStore = usePlacesStore()

const { department, userPosition } = storeToRefs(userStore)
//const { places } = storeToRefs(placesStore)

const loading = ref(true)
const nearbyRadiusKm = 50

const distanceInKm = (place, position) => {
	const latitude = Number(place.latitude)
	const longitude = Number(place.longitude)
	const userLatitude = Number(position.latitude)
	const userLongitude = Number(position.longitude)

	if (![latitude, longitude, userLatitude, userLongitude].every(Number.isFinite)) {
		return null
	}

	const toRadians = degrees => degrees * Math.PI / 180
	const latitudeDifference = toRadians(userLatitude - latitude)
	const longitudeDifference = toRadians(userLongitude - longitude)
	const haversine = Math.sin(latitudeDifference / 2) ** 2
		+ Math.cos(toRadians(latitude)) * Math.cos(toRadians(userLatitude))
		* Math.sin(longitudeDifference / 2) ** 2

	return 6371 * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine))
}

const getPlaceImageStyle = place => {
	const imageName = place.cover_image?.[0]?.name || 'defaultpicture.jpg'

	return {
		backgroundImage: `url("/images/${encodeURIComponent(imageName)}")`,
		backgroundPosition: 'center',
		backgroundSize: 'cover'
	}
}

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
			placesStore.storePlaces(response.data.data)
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

// ***************** on récupère les 3 endroits les mieux notés + les 3 derniers postés ****************
const getThreeTopAndLastPlaces = () => {
	if (!Array.isArray(placesStore.places)) {
		placesStore.storeThreeTopPlaces([])
		placesStore.storeThreeLastPlaces([])
		return
	}

	const position = userPosition.value
	const hasPosition = position
		&& Number.isFinite(Number(position.latitude))
		&& Number.isFinite(Number(position.longitude))
	const departmentId = department.value?.id ?? department.value
	let filteredPlacesList = departmentId
		? placesStore.places.filter(place =>
			String(place.department?.id ?? place.department_id) === String(departmentId))
		: placesStore.places

	if (hasPosition) {
		const placesByDistance = placesStore.places
			.map(place => ({ ...place, distanceKm: distanceInKm(place, position) }))
			.filter(place => place.distanceKm !== null)
			.sort((a, b) => a.distanceKm - b.distanceKm)
		const nearbyPlaces = placesByDistance.filter(place => place.distanceKm <= nearbyRadiusKm)

		filteredPlacesList = nearbyPlaces.length
			? nearbyPlaces
			: placesByDistance.slice(0, 3)
	}

	const threeTopPlaces = [...filteredPlacesList]
		.sort((a, b) => Number(b.note) - Number(a.note))
		.slice(0, 3)
	const threeLastPlaces = [...filteredPlacesList]
		.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
		.slice(0, 3)

	placesStore.storeThreeTopPlaces(threeTopPlaces)
	placesStore.storeThreeLastPlaces(threeLastPlaces)
}

watch(
	[department, userPosition, () => placesStore.places],
	getThreeTopAndLastPlaces,
	{ immediate: true }
)

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
	///getFavorites() => erreur : get non accepté au lieu de post ?

	loading.value = false
})


// onmounted chat gpt
// onMounted(async () => {
// 	if (!placesStore.categories) {
// 		try {
// 			const response = await axios.get('/api/categories');
// 			placesStore.storeCategories(response.data.data || []);
// 		} catch {
// 			alert('Une erreur s\'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.');
// 		}
// 	}
// });
</script>

<template>
	<div class="text-center app-layout">

		<!-- ******************************************* HEADER *********************************************** -->

		<Header />

		<main class="app-content">
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

			<div class="container-fluid p-5" id="presentation">
				<div class="row pb-4">
					<div class="col-md-6 my-auto">
						<img id="bigLogo" class="w-75 h-75 bg-white rounded" src="../../public/images/logo.png"
							alt="logo nice places">
					</div>

					<div class="col-md-6 my-auto">
						<p class="text-white fs-3">Nice places est une plateforme communautaire simple à utiliser, pour trouver et
							partager
							des
							sorties nature
							(plage,
							forêt, lac, montagne...) près de chez vous. Plages, forêts, lacs, montagne, parcs urbains.. Il y a toujours une nouvelle balade à découvrir !</p>
					</div>
				</div>

				<router-link to="/inscription"><button class="btn btn-lg greenButton mt-3 rounded-pill">Je
						m'inscris</button>
				</router-link>
			</div>

			<div id="presentationBottomStripe" class="mb-3"></div>

			<div class="home-section-heading">
				<i class="greenIcon mx-auto fa-2x fa-solid fa-map-location-dot"></i>
				<h2 class="fs-2 m-3">Carte des lieux autour de chez vous</h2>
			</div>


			<!-- *********************************************** CARTE ************************************************** -->

			<Map :singlePlace="null" />

			<!-- *********************************************** CATEGORIES ************************************************** -->

			<div class="container-fluid home-section" v-if="!loading && placesStore.categories">

				<div class="home-section-heading">
					<i class="greenIcon mx-auto fa-2x fa-solid fa-book-atlas"></i>
					<h2 class="fs-2 m-3">Catégories</h2>
				</div>

				<div class="row m-1 p-1" id="categories">

					<!-- ********************** boucle qui affiche les 3 catégories avec le + de lieux ************************* -->

					<router-link v-for="category in placesStore.categories.slice(0, 3)" :key="category.id"
						:to="`/categories/${category.id}`"
						class="category-image-overlay mx-auto col-md-6 col-lg-4 p-4 border border-white d-flex align-items-center justify-content-center text-decoration-none"
						:style="`background-image: url('/images/category${category.id}.jpg'); background-position: center; background-size: cover;`">
						<p class="fs-2 m-0 text-white textWithShadow">{{ category.name }}</p>
					</router-link>

				</div>

				<router-link to="/categories"><button class="btn btn-lg blueButton mt-3 rounded-pill">Toutes les
						catégories</button>
				</router-link>

			</div>


			<!-- ******************************************* TOP DES LIEUX *********************************************** -->

			<div v-if="placesStore.threeTopPlaces?.length" class="home-section">
				<div class="home-section-heading">
					<i class="greenIcon mx-auto fa-2x fa-solid fa-star"></i>
					<h2 v-if="userPosition?.latitude != null" class="fs-2 m-3">Le top des lieux près de vous</h2>
					<h2 v-else-if="department" class="fs-2 m-3">Le top des lieux dans votre département</h2>
					<h2 v-else class="fs-2 m-3">Le top des lieux (France entière)</h2>
				</div>

				<div class="container-fluid">

					<div class="row m-1 p-1" id="topplaces">

						<!-- ********************** boucle qui affiche les 3 places ************************* -->

						<div v-for="(topPlace, index) in placesStore.threeTopPlaces" :key="topPlace.id"
							class="mx-auto col-md-6 col-lg-4 p-2 border border-white d-flex flex-column justify-content-between"
							:style="getPlaceImageStyle(topPlace)">
							<div class="home-place-info">
								<div class="home-place-heading">
									<span class="ranking">#{{ index + 1 }}</span>
									<h5>{{ topPlace.name }}</h5>
								</div>
								<div class="home-place-meta">
									<span class="home-place-meta-item">
										<i class="yellowStar fa-solid fa-star home-place-meta-icon"></i>
										{{ topPlace.note }}
									</span>
									<span class="home-place-meta-item">
										<span class="home-place-category-icon" v-html="topPlace.category?.icon"></span>
										{{ topPlace.category?.name }}
									</span>
									<span class="home-place-meta-item">
										<i class="greenIcon fa-solid fa-location-dot home-place-meta-icon"></i>
										{{ topPlace.city }} ({{ String(topPlace.postcode ?? '').substring(0, 2) }})
										<span v-if="topPlace.distanceKm != null">· {{ topPlace.distanceKm.toFixed(1) }} km</span>
									</span>
								</div>
							</div>

							<router-link :to="`/lieu/${topPlace.id}`"><button
									class="btn btn-lg greenButton rounded-pill">découvrir</button></router-link>

						</div>

					</div>

					<router-link to="/toplieux"><button class="btn btn-lg blueButton mt-3 rounded-pill">Classement
							complet</button>
					</router-link>

				</div>
			</div>


			<!-- ********************************* DERNIERS LIEUX AJOUTES *************************************** -->

			<div v-if="placesStore.threeLastPlaces?.length" class="home-section">
				<div class="home-section-heading">
					<i class="greenIcon mx-auto fa-2x fa-solid fa-clock"></i>
					<h2 v-if="userPosition?.latitude != null" class="fs-2 m-3">Derniers lieux ajoutés près de vous</h2>
					<h2 v-else-if="department" class="fs-2 m-3">Derniers lieux ajoutés dans votre département</h2>
					<h2 v-else class="fs-2 m-3">Derniers lieux ajoutés (France entière)</h2>
				</div>

				<div class="container-fluid">

					<div class="row m-1 p-1" id="derniersplaces">

						<!-- ********************** boucle qui affiche les 3 derniers places ************************* -->

						<div v-for="lastPlace in placesStore.threeLastPlaces" :key="lastPlace.id"
							class="mx-auto col-md-6 col-lg-4 p-2 border border-white d-flex flex-column justify-content-between"
							:style="getPlaceImageStyle(lastPlace)">
							<div class="home-place-info">
								<div class="home-place-heading">
									<span class="home-place-category-icon" v-html="lastPlace.category?.icon"></span>
									<h5>{{ lastPlace.name }}</h5>
								</div>
								<div class="home-place-meta">
									<span class="home-place-meta-item">
										<i class="greenIcon fa-solid fa-location-dot home-place-meta-icon"></i>
										{{ lastPlace.city }} ({{ String(lastPlace.postcode ?? '').substring(0, 2) }})
										<span v-if="lastPlace.distanceKm != null">· {{ lastPlace.distanceKm.toFixed(1) }} km</span>
									</span>
									<span v-if="lastPlace.user" class="home-place-meta-item">
										<i class="greenIcon fa-solid fa-user home-place-meta-icon"></i>
										par {{ lastPlace.user.name }}
									</span>
								</div>
							</div>

							<router-link :to="`/lieu/${lastPlace.id}`"><button
									class="btn btn-lg greenButton rounded-pill">découvrir</button></router-link>

						</div>

					</div>
				</div>

				<router-link to="/proposerlieu"><button class="btn btn-lg blueButton mt-3 rounded-pill">Proposer un
						lieu</button>
				</router-link>

			</div>

			<!-- *********************************** INSCRIVEZ-VOUS ************************************** -->

			<section id="inscrivezVous">

				<i class="greenIcon mt-5 mx-auto fa-2x fa-solid fa-user-plus"></i>
				<h2 class="fs-2 m-3">Inscrivez-vous gratuitement !</h2>

				<div class="container-fluid px-5 mt-4">
					<div class="row" id="inscrivezVousIcones">
						<div class="col-md-4 p-1">
							<i class="signup-feature-icon fa-solid fa-earth-europe" aria-hidden="true"></i>
							<p class="fs-4 mt-3">Des lieux dans toute la France</p>
						</div>

						<div class="col-md-4 p-1">
							<i class="signup-feature-icon fa-solid fa-location-dot" aria-hidden="true"></i>
							<p class="fs-4 mt-3">Partagez vos coins préférés</p>
						</div>

						<div class="col-md-4 p-1">
							<i class="signup-feature-icon fa-solid fa-people-group" aria-hidden="true"></i>
							<p class="fs-4 mt-3">Rejoignez la communauté</p>
						</div>
					</div>
				</div>

				<router-link to="/inscription"><button class="btn btn-lg blueButton mt-3 rounded-pill">C'est décidé, je
						m'inscris !</button>
				</router-link>
			</section>

		</div>
		</main>

	<Footer />
	</div>
</template>

<style>
/******************* REGLES GENERALES ************/

#app {
	min-height: 100vh;
}

.app-layout {
	min-height: 100vh;
	display: flex;
	flex-direction: column;
}

.app-content {
	flex: 1 0 auto;
}

footer {
	flex-shrink: 0;
	margin-top: auto !important;
}

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

#presentation {
	background-image: url(../../public/images/mervent2.jpg)
}

/******************* boutons ************/

.greenButton,
.blueButton {
	font-family: "Cooper", system-ui, sans-serif;
	font-weight: 700;
	color: #1C6E8C !important;
	background-color: #94D1BE;
	border: 0;
	border-radius: 20px;
	transition: color 150ms ease, background-color 150ms ease, transform 150ms ease;
}

.greenButton:hover,
.greenButton:focus-visible,
.blueButton:hover,
.blueButton:focus-visible {
	color: white !important;
	background-color: #1C6E8C;
}

.greenButton:active,
.blueButton:active {
	transform: translateY(1px);
}

.greenButton:disabled,
.blueButton:disabled {
	transform: none;
}

/************* ombres ************/

.textWithShadow {
	text-shadow: 2px 2px 4px #1C6E8C;
}

.category-image-overlay {
	font-family: "Cooper", system-ui, sans-serif;
	min-height: 50vh;
	margin-bottom: 1rem;
	background-repeat: no-repeat;
	background-position: center;
	background-size: cover;
}

.home-section {
	margin-top: 5rem;
}

.home-section-heading {
	margin: 4rem 0 1.5rem;
}

.home-section-heading h2 {
	margin-bottom: 0;
}

#categories {
	row-gap: 1rem;
}

.iconWithShadow {
	text-shadow: 2px 2px 4px #fff;
}

/************* Textes et polices ************/

.bigFontSize {
	font-size: 2em;
}

.bigFontSize2 {
	margin-top: -4vh;
	font-size: 3em;
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

.home-place-info {
	background-color: rgba(50, 61, 158, 0.5);
	padding: 0.75rem;
	text-align: left;
}

.home-place-heading {
	display: flex;
	align-items: center;
	gap: 0.65rem;
	margin-bottom: 0.65rem;
}

.home-place-heading h5 {
	margin: 0;
	color: white;
	font-size: 1rem;
	text-shadow: 1px 1px 2px #1c6e8c;
}

.home-place-meta {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: 0.4rem 1rem;
	color: white;
	font-size: 0.9rem;
	line-height: 1.4;
}

.home-place-meta-item {
	display: inline-flex;
	align-items: center;
	gap: 0.35rem;
	min-width: 0;
}

.home-place-meta-icon {
	flex: 0 0 1rem;
	width: 1rem;
	font-size: 1rem;
	text-align: center;
}

.home-place-category-icon {
	display: inline-flex;
	flex: 0 0 1.25rem;
	align-items: center;
	justify-content: center;
	width: 1.25rem;
	height: 1.25rem;
	font-size: 1.1rem;
	line-height: 1;
	text-align: center;
}

.home-place-category-icon svg {
	width: 1em;
	height: 1em;
}

/******************* inscrivez-vous ************/


#inscrivezVous p {
	color: #1c6e8c;
}

#inscrivezVous {
	margin: 4rem 0 6rem;
}

#inscrivezVousIcones {
	row-gap: 1.5rem;
}

#inscrivezVous .signup-feature-icon {
	display: inline-flex;
	width: 5.5rem;
	height: 5.5rem;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background-color: #e8f5f1;
	color: #1c6e8c;
	font-size: 2.5rem;
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

	.category-image-overlay {
		min-height: 40vh;
	}

	.home-section {
		margin-top: 4rem;
	}

	.home-section-heading {
		margin: 3rem 0 1.25rem;
	}

	#inscrivezVous {
		margin: 3rem 0 4rem;
	}

	#presentation {
		height: 62vh
	}

	.bigFontSize2 {
		margin-top: -3vh;
		font-size: 2.5em;
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

	.category-image-overlay {
		min-height: 42.5vh;
	}

}

@media screen and (min-width: 769px) and (max-width: 992px) {

	#topplaces,
	#derniersplaces {
		height: 60vh;
	}

	.category-image-overlay {
		min-height: 30vh;
	}
}

@media screen and (max-width: 768px) {
	.bigFontSize {
		font-size: 1.75em;
	}
}
</style>