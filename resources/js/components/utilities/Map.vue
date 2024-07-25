<template>
    <div id="map" ref="map"></div>
</template>

<script setup>
import axios from "axios";
import { useUserStore } from "../../stores/userStore";
import { usePlacesStore } from '../../stores/placesStore';
import { useRouter } from "vue-router";
import { onBeforeMount, onMounted, ref, watch } from "vue";

const userStore = useUserStore();
const placesStore = usePlacesStore();
const router = useRouter();
const map = ref(null);

const props = defineProps(['singlePlace']);

// Assurez-vous que categories est défini et initialisé correctement
let categories = []; // ou définir par défaut un tableau vide
let places = []; // Initialiser places s'il est également utilisé

const initializeMap = () => {
    if (!map.value) return;

    let latitude, longitude;

    if (props.singlePlace) {
        // ******************* si page détails => affichage d'un seul place (pas de pointeur user) *****************
        latitude = props.singlePlace.latitude;
        longitude = props.singlePlace.longitude;

    } else if (userStore.userPosition) {
        // **************** si le user a accepté la géoloc => on crée une map avec son emplacement *****************
        latitude = userStore.userPosition.latitude;
        longitude = userStore.userPosition.longitude;

    } else {
        // **** si le user n'a pas accepté la géoloc => on crée une map avec un emplacement par défaut (Paris) ****
        latitude = 48.8534100;
        longitude = 2.3488000;
    }

    // ***************************************** initialisation map  ******************************************************

    map.value = L.map('map').setView([latitude, longitude], 13);

    // pour pouvoir ajouter des tuiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map.value);

    // ******************* si page détails => création pointeur du place *****************
    if (props.singlePlace) {
        L.marker([latitude, longitude]).addTo(map.value);

        // ******************* sinon => création pointeur uilisateur *****************
    } else if (userStore.userPosition) {
        L.marker([latitude, longitude]).addTo(map.value);
    }

    // ********************************  initialisation panneau de contrôle  ***********************************************

    if (!props.singlePlace && Array.isArray(categories) && categories.length > 0) {

        // Ajout du panneau de contrôle et des marqueurs
        let command = L.control({ position: 'topright' });

        // onAdd = Should contain code that creates DOM elements for the layer, adds them to map panes where they should belong and puts 
        // listeners on relevant map events. Called on map.addLayer(layer).
        command.onAdd = function () {
            // création de la div qui va contenir le panneau de contrôle
            let div = L.DomUtil.create('div', 'p-3');

            // ajout de style avec des classes Bootstrap
            L.DomUtil.addClass(div, 'bg-white text-left border border-secondary rounded d-flex flex-column align-items-start');

            // ajout du titre
            div.innerHTML += '<div class="mb-2 mx-auto"><span style="font-size:18px; font-family:cooper; color: #1c6e8c">Filtrer par catégorie</span></div>';

            // création d'une checkbox par catégorie
            categories.forEach(category => {
                div.innerHTML += `<form style="font-size: 14px"><input id="${category.id}" class="categoriesForm" type="checkbox" checked class="me-2"/> ${category.nom} <span style="color:${category.couleur}">${category.icone}</span></form>`;
            });
            return div;
        };

        command.addTo(map.value);

        // ************************************on ajoute un pointeur par place à la map***********************************************

        categories.forEach(category => { // on boucle sur les catégories 

            let categoryGroup = L.featureGroup(); // on crée un groupe pour la catégorie

            places.forEach(place => { // on boucle sur les lieux et on crée un marqueur pour chaque avec son popup

                if (place.category.id == category.id) { // si la catégorie du place est bien la catégorie concernée, on crée le marqueur et son pointeur

                    let imageCouvertureplace = place.cover_image[0] ? place.cover_image[0].nom : 'defaultpicture.jpg';

                    let popupContent = `<div class="text-center"><span style="display:none">${place.id}</span>
                                        <div><span class="mx-auto fa-2x" style="color:${place.category.couleur}">${place.category.icone}</span>
                                        <i class="fa-solid fa-star ms-3 me-2" style="color: yellow"></i>
                                        <span class="fs-5 text-secondary" style="font-family:'Cooper'">${place.note}</span></div>
                                        <h5 style="color: #1C6E8C; font-family:'Cooper'">${place.name}</h5>
                                        <img class="mx-auto" src="images/${imageCouvertureplace}" alt="image représentant le place" style="width:35vw">
                                        <p style="font-family:'Cooper'" class="text-center text-secondary">${place.adress}<br>${place.postcode} ${place.city}</p></div>`;

                    let popupOptions = { 'maxWidth': '35vw', 'className': 'popupplace' }; // on choisit les options du popup

                    // on crée un icône correspondant à celui choisi pour la catégorie
                    const fontAwesomeIcon = L.divIcon({ html: `<span style="text-shadow: 2px 2px 4px #fff; color: ${category.color}">${category.icon}</span>`, iconSize: [40, 40], className: `fa-2x ${category.name}` });

                    // on ajoute le marqueur au groupe avec addTo et on lui associe son groupe avec bindPopup
                    L.marker([place.latitude, place.longitude], { icon: fontAwesomeIcon }).addTo(categoryGroup).bindPopup(popupContent, popupOptions);
                }
            });
            category.groupe = categoryGroup;       // on ajoute le groupe à la catégorie (pour afficher / masquer via panneau de contrôle)

            map.value.addLayer(categoryGroup);     // on ajoute le groupe à la map

        });

        map.value.on('popupopen', function () {
            // on cible le popup ouvert
            let popup = document.getElementsByClassName('leaflet-popup-content')[0];

            // on récupère l'id du place présent dans un span invisible au début du popup (1er enfant de la div qui englobe le contenu)
            let placeId = popup.firstChild.firstChild.innerHTML

            // on crée le bouton avec des classes pour le styliser
            let btn = document.createElement('button')
            btn.textContent = 'Plus d\'infos';
            btn.classList.add('btn', 'btn-lg', 'greenButton');

            // on met en place un évènement pour déclencher la redirection vers la page du place en cas de clic sur le bouton
            btn.addEventListener("click", function () {
                router.push('/place/' + placeId)
            });

            // on crée une div qui contiendra le bouton (pour qu'il soit centré)
            let div = document.createElement('div')
            div.classList.add('text-center')

            // on ajoute le bouton à la div et la div au popup
            div.appendChild(btn)
            popup.appendChild(div)
        })
    }
}

// onBeforeMount(() => {
//     if (!placesStore.places) {
//         axios.get("/api/places")
//             .then(response => {
//                 placesStore.storeplaces(response.data.data);
//                 places = response.data.data;
//                 // Supposez que vous récupérez également les catégories de l'API
//                 categories = response.data.categories || [];
//             }).catch(() => {
//                 alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.");
//             });
//     } else {
//         places = placesStore.places;
//         // Supposez que vous avez les catégories dans le store également
//         categories = placesStore.categories || [];
//     }
// });

// code chatgpt / la carte marche sans
// avec ou sans : pas d'affichage des catégories + pointeurs
// watch(
//   () => placesStore.categories,
//   (newCategories) => {
//     if (newCategories.length > 0) {
//       initializeMap();
//     }
//   },
//   { immediate: true }
// );

onMounted(() => {
    if (!userStore.geolocationAnswered) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                userStore.storeGeolocationAnswered(true);
                userStore.storeUserPosition({ latitude: position.coords.latitude, longitude: position.coords.longitude });
                initializeMap();
            }, () => {
                userStore.storeGeolocationAnswered(true);
                initializeMap();
            });
        } else {
            alert("La géolocalisation est indisponible sur votre navigateur");
            initializeMap();
        }
    } else {
        initializeMap();
    }
});
</script>

<style scoped>
#map {
    height: 75vh;
    margin: auto;
}
</style>
