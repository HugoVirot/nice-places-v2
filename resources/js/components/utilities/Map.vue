<template>
    <div id="map" ref="map"></div>
</template>

<script>
import axios from "axios";
import { useUserStore } from "../../stores/userStore";
import { useLieuxStore } from '../../stores/lieuxStore'
import { mapState } from "pinia";
import { mapActions } from 'pinia'

export default {

    computed: {
        ...mapState(useLieuxStore, [
            'categories',
            'lieux'
        ]),

        ...mapState(useUserStore, ['geolocationAnswered',
            'userPosition'])
    },

    data() {
        return {
            map: ""
        }
    },

    props: ["lieuSeul"],

    methods: {

        ...mapActions(useUserStore, ['storeGeolocationAnswered', 'storeUserPosition']),

        ...mapActions(useLieuxStore, ['storeLieux']),


        initializeMap(component) {
            if (this.$refs.map) {
                // ******************* si page détails => affichage d'un seul lieu (pas de pointeur user pour le moment, confusion) *****************
                if (component.lieuSeul) {

                    const latitude = component.lieuSeul.latitude
                    const longitude = component.lieuSeul.longitude

                    component.map = new L.map('map').setView([latitude, longitude], 13);

                    // pour pouvoir ajouter des tuiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(component.map);

                    L.marker([latitude, longitude]).addTo(component.map)

                } else {

                    // **************** si le user a accepté la géoloc => on crée une map avec son emplacement *****************
                    if (this.userPosition) {
                        // console.log("géoloc déjà acceptée, coordonnées déjà stockées dans le state")

                        component.map = L.map('map').setView([this.userPosition.latitude, this.userPosition.longitude], 13);

                        // pour pouvoir ajouter des tuiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(component.map);

                        // création du pointeur indiquant la position de l'utilisateur
                        L.marker([this.userPosition.latitude, this.userPosition.longitude]).addTo(component.map);


                        // **** si le user n'a pas accepté la géoloc => on crée une map avec un emplacement par défaut (Paris) ****
                    } else {

                        component.map = L.map('map').setView([48.8534100, 2.3488000], 13);

                        // pour pouvoir ajouter des tuiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(component.map);
                    }


                    // *****************************************  panneau de contrôle  ******************************************************

                    let command = L.control({ position: 'topright' });
                    let categories = component.categories

                    // onAdd = Should contain code that creates DOM elements for the layer, adds them to map panes where they should belong and puts 
                    // listeners on relevant map events. Called on map.addLayer(layer).
                    command.onAdd = function () {

                        // création de la div qui va contenir le panneau de contrôle
                        let div = L.DomUtil.create('div', 'p-3');

                        // 99ajout de style avec des classes Bootstrap
                        L.DomUtil.addClass(div, 'bg-white')
                        L.DomUtil.addClass(div, 'text-left')
                        L.DomUtil.addClass(div, 'border')
                        L.DomUtil.addClass(div, 'border-secondary')
                        L.DomUtil.addClass(div, 'rounded')
                        L.DomUtil.addClass(div, 'd-flex')
                        L.DomUtil.addClass(div, 'flex-column')
                        L.DomUtil.addClass(div, 'align-items-start')

                        // ajout du titre
                        div.innerHTML += '<div class="mb-2 mx-auto"><span style="font-size:18px; font-family:cooper; color: #1c6e8c">Filtrer par catégorie</span></div>';

                        // création d'une checkbox par catégorie
                        for (let i = 0; i < categories.length; i++) {
                            div.innerHTML += '<form style="font-size: 14px"><input id="' + categories[i].id + '" class="categoriesForm" type="checkbox" checked class=\"me-2\"/>' + ' '
                                + categories[i].nom + ' <span style="color:' + categories[i].couleur + '">' + categories[i].icone + '</span></form>';
                        }

                        return div;
                    };

                    command.addTo(component.map);


                    // *****************************************  listener sur cochage / décochage checkbox  ******************************************************

                    for (var i = 0; i < categories.length; i++) {
                        document.getElementById(categories[i].id).addEventListener("click", handleCommand, false);
                    }

                    function handleCommand() {

                        let selectedCategory;

                        for (var i = 0; i < categories.length; i++) {
                            if (categories[i].id == this.id) {
                                selectedCategory = categories[i];
                                break;
                            }
                        }

                        if (this.checked) {

                            selectedCategory.groupe.addTo(component.map);
                        } else {
                            component.map.removeLayer(selectedCategory.groupe);
                        }
                    }


                    // ************************************on ajoute un pointeur par lieu à la map***********************************************

                    if (categories) {

                        categories.forEach(categorie => { // on boucle sur les catégories 

                            let groupeCatégorie = L.featureGroup(); // on crée un groupe pour la catégorie

                            component.lieux.forEach(lieu => {  // on boucle sur les lieux et on crée un marqueur pour chaque avec son popup

                                if (lieu.categorie.id == categorie.id) { // si la catégorie du lieu est bien la catégorie concernée, on crée le marqueur et son pointeur

                                    let imageCouvertureLieu = lieu.image_mise_en_avant[0] ? lieu.image_mise_en_avant[0].nom : 'defaultpicture.jpg'

                                    let popupContent = "<div class=\"text-center\"><span style=\"display:none\">" + lieu.id + "</span>" +
                                        "<div><span class=\"mx-auto fa-2x\" style=\"color:" + lieu.categorie.couleur + "\">" + lieu.categorie.icone + "</span>" +
                                        "<i class=\"fa-solid fa-star ms-3 me-2\" style=\"color: yellow\"></i>" +
                                        "<span class=\"fs-5 text-secondary\" style=\"font-family:'Cooper'\">" + lieu.note + "</span></div>" +
                                        "<h5 style=\"color: #1C6E8C; font-family:'Cooper'\">" + lieu.nom + "</h5>" +
                                        "<img class=\"mx-auto\" src=\"images/" + imageCouvertureLieu + "\" alt=\"image représentant le lieu\" style=\"width:35vw\">" +
                                        "<p style=\"font-family:'Cooper'\" class=\"text-center text-secondary\">" + lieu.adresse + "<br>" + lieu.code_postal + " " + lieu.ville + "</p></div>"

                                    let popupOptions =  // on choisit les options du popup
                                    {
                                        'maxWidth': '35vw',
                                        'className': 'popupLieu'
                                    }

                                    const fontAwesomeIcon = L.divIcon({   // on crée un icône correspondant à celui choisi pour la catégorie
                                        html: `<span style="text-shadow: 2px 2px 4px #fff; color: ${categorie.couleur}">${categorie.icone}</span>`,
                                        iconSize: [40, 40],
                                        className: 'fa-2x ' + categorie.nom,
                                    });

                                    L.marker([lieu.latitude, lieu.longitude], { icon: fontAwesomeIcon })
                                        .addTo(groupeCatégorie)   // on ajoute le marqueur au groupe
                                        .bindPopup(popupContent, popupOptions) // on lui associe son popup

                                }
                            })

                            // on ajoute le groupe à la catégorie (pour afficher / masquer via panneau de contrôle)
                            categorie.groupe = groupeCatégorie

                            // on ajoute le groupe à la map
                            component.map.addLayer(groupeCatégorie);
                        })


                        // ******* on ajoute un bouton sur le popup quand il s'ouvre. Au clic => redirection vers la page "Détails du lieu" *********

                        component.map.on('popupopen', function () {

                            // on cible le popup ouvert
                            let popup = document.getElementsByClassName('leaflet-popup-content')[0];

                            // on récupère l'id du lieu présent dans un span invisible au début du popup (1er enfant de la div qui englobe le contenu)
                            let lieuId = popup.firstChild.firstChild.innerHTML

                            // on crée le bouton avec des classes pour le styliser
                            let btn = document.createElement('button')
                            btn.textContent = 'Plus d\'infos';
                            btn.classList.add('btn', 'btn-lg', 'greenButton');

                            // on met en place un évènement pour déclencher la redirection vers la page du lieu en cas de clic sur le bouton
                            btn.addEventListener("click", function () {
                                component.$router.push('/lieu/' + lieuId)
                            });

                            // on crée une div qui contiendra le bouton (pour qu'il soit centré)
                            let div = document.createElement('div')
                            div.classList.add('text-center')

                            // on ajoute le bouton à la div et la div au popup
                            div.appendChild(btn)
                            popup.appendChild(div)
                        }

                        )
                    }
                }
            }
        }
    },

    created() {
        // si les lieux ne sont pas encore récupérés, on va les chercher
        if (!this.lieux) {
            axios.get("/api/lieus")
                .then(response => {
                    // on stocke les lieux dans le state
                    this.storeLieux(response.data.data)
                }).catch(() => { // message d'erreur pour l'utilisateur en cas d'échec de l'appel API
                    alert("Une erreur s'est produite. Certains éléments peuvent ne pas être affichés. Vous pouvez essayer de recharger la page pour corriger le problème.")
                })
        }
    },

    mounted() {

        // *********************si géolocalisation pas déjà demandée***********************
        if (!this.geolocationAnswered) {

            // ************************ on la demande ************************

            // si le navigateur supporte la géolocalisation (pas dispo sur les + anciens)
            if (navigator.geolocation) {

                // on demande l'accès à la position via une petite fenêtre 
                // si accepté, on stocke les coordonnées de l'utilisateur dans le state
                navigator.geolocation.getCurrentPosition(position => {
                    this.storeGeolocationAnswered(true)
                    this.storeUserPosition({ latitude: position.coords.latitude, longitude: position.coords.longitude })
                    //console.log("accès position accepté, choix et coordonnées stockés dans le state")
                    this.initializeMap(this)

                    // ici la fonction anonyme est lancée en cas de refus (fonction dite "de callback")                    
                }, () => { // si accès refusé, on stocke cela dans le state et on l'affiche dans la console
                    this.storeGeolocationAnswered(true)
                    // console.log("accès à la position refusé, choix stocké dans le state")
                    this.initializeMap(this)
                }
                )
            } else {
                alert("La géolocalisation est indisponible sur votre navigateur")
                this.initializeMap(this)
            }

            // *********************si géolocalisation déjà demandée***********************
        } else {
            // *************** on initialise la map *****************
            this.initializeMap(this)
        }
    }
}
</script>

<style scoped>
#map {
    height: 75vh;
    margin: auto;
}
</style>
