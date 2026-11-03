<template>
    <div id="map" ref="mapElement"></div>
</template>

<script setup>
import { useUserStore } from "../../stores/userStore";
import { usePlacesStore } from '../../stores/placesStore';
import { onBeforeUnmount, onMounted, ref, watch } from "vue";

const userStore = useUserStore();
const placesStore = usePlacesStore();
const mapElement = ref(null);

const props = defineProps(['singlePlace']);
let leafletMap = null;
let placesLayer = null;
let userMarker = null;
let fullscreenButton = null;

const updateFullscreenButton = () => {
    if (!fullscreenButton) return;

    const isFullscreen = document.fullscreenElement === mapElement.value
        || mapElement.value?.classList.contains('map-fullscreen');
    fullscreenButton.innerHTML = `<i class="fa-solid ${isFullscreen ? 'fa-compress' : 'fa-expand'}" aria-hidden="true"></i>`;
    fullscreenButton.title = isFullscreen ? 'Quitter le plein écran' : 'Plein écran';
    fullscreenButton.setAttribute('aria-label', fullscreenButton.title);
    requestAnimationFrame(() => leafletMap?.invalidateSize());
};

const toggleFullscreen = () => {
    if (document.fullscreenElement === mapElement.value) {
        document.exitFullscreen?.()?.catch(() => {});
        return;
    }

    if (mapElement.value?.classList.contains('map-fullscreen')) {
        mapElement.value.classList.remove('map-fullscreen');
        updateFullscreenButton();
        return;
    }

    const useCssFullscreen = () => {
        mapElement.value?.classList.add('map-fullscreen');
        updateFullscreenButton();
    };

    try {
        const request = mapElement.value?.requestFullscreen?.();
        if (request?.then) {
            request.then(updateFullscreenButton).catch(useCssFullscreen);
        } else {
            useCssFullscreen();
        }
    } catch {
        useCssFullscreen();
    }
};

const addPlaceMarkers = () => {
    if (!placesLayer || props.singlePlace || !Array.isArray(placesStore.places)) return;

    placesLayer.clearLayers();

    placesStore.places.forEach(place => {
        const latitude = Number(place.latitude);
        const longitude = Number(place.longitude);
        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

        const category = place.category ?? placesStore.categories?.find(item => item.id === place.category_id);
        const markerOptions = category?.icon ? {
            icon: L.divIcon({
                html: `<span style="color:${category.color || '#1c6e8c'}">${category.icon}</span>`,
                className: 'fa-2x',
                iconSize: [40, 40]
            })
        } : {};

        const popup = document.createElement('div');
        popup.className = 'text-center';

        const ratingRow = document.createElement('div');
        const categoryIcon = document.createElement('span');
        categoryIcon.className = 'mx-auto fa-2x';
        categoryIcon.style.color = category?.color || '#1c6e8c';
        categoryIcon.innerHTML = category?.icon || '';
        ratingRow.appendChild(categoryIcon);

        const star = document.createElement('i');
        star.className = 'fa-solid fa-star ms-3 me-2';
        star.style.color = 'yellow';
        ratingRow.appendChild(star);

        const rating = document.createElement('span');
        rating.className = 'fs-5 text-secondary';
        rating.style.fontFamily = 'Cooper';
        rating.textContent = place.note;
        ratingRow.appendChild(rating);
        popup.appendChild(ratingRow);

        const title = document.createElement('h5');
        title.style.color = '#1c6e8c';
        title.style.fontFamily = 'Cooper';
        title.textContent = place.name;
        popup.appendChild(title);

        const coverImage = document.createElement('img');
        coverImage.className = 'mx-auto';
        coverImage.src = `/images/${encodeURIComponent(place.cover_image?.[0]?.name || 'defaultpicture.jpg')}`;
        coverImage.alt = `Photo de ${place.name}`;
        coverImage.style.width = 'min(35vw, 320px)';
        coverImage.style.height = 'auto';
        popup.appendChild(coverImage);

        const address = document.createElement('p');
        address.className = 'text-center text-secondary';
        address.style.fontFamily = 'Cooper';
        address.append(document.createTextNode(place.adress || ''));
        address.appendChild(document.createElement('br'));
        address.append(document.createTextNode(`${place.postcode || ''} ${place.city || ''}`.trim()));
        popup.appendChild(address);

        const detailsLink = document.createElement('a');
        detailsLink.href = `/place/${place.id}`;
        detailsLink.className = 'btn btn-lg greenButton';
        detailsLink.textContent = "Plus d'infos";
        popup.appendChild(detailsLink);

        L.marker([latitude, longitude], markerOptions)
            .bindPopup(popup, { maxWidth: 350, className: 'popupplace' })
            .addTo(placesLayer);
    });
};

const initializeMap = () => {
    if (!mapElement.value || leafletMap) return;

    const latitude = props.singlePlace?.latitude ?? userStore.userPosition?.latitude ?? 48.85341;
    const longitude = props.singlePlace?.longitude ?? userStore.userPosition?.longitude ?? 2.3488;

    leafletMap = L.map(mapElement.value).setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(leafletMap);

    const fullscreenControl = L.control({ position: 'topright' });
    fullscreenControl.onAdd = () => {
        fullscreenButton = L.DomUtil.create('button', 'leaflet-bar map-fullscreen-button');
        fullscreenButton.type = 'button';
        fullscreenButton.innerHTML = '<i class="fa-solid fa-expand" aria-hidden="true"></i>';
        fullscreenButton.title = 'Plein écran';
        fullscreenButton.setAttribute('aria-label', 'Plein écran');
        L.DomEvent.disableClickPropagation(fullscreenButton);
        fullscreenButton.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            toggleFullscreen();
        });
        return fullscreenButton;
    };
    fullscreenControl.addTo(leafletMap);

    if (props.singlePlace) {
        L.marker([latitude, longitude]).addTo(leafletMap);
        return;
    }

    if (userStore.userPosition) {
        L.marker([latitude, longitude]).addTo(leafletMap);
    }

    placesLayer = L.layerGroup().addTo(leafletMap);
    addPlaceMarkers();
};

watch(
    [() => placesStore.places, () => placesStore.categories],
    addPlaceMarkers
);

onMounted(() => {
    document.addEventListener('fullscreenchange', updateFullscreenButton);
    initializeMap();

    if (props.singlePlace) {
        return;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            userStore.storeGeolocationAnswered(true);
            userStore.storeUserPosition({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            });
            const coordinates = [position.coords.latitude, position.coords.longitude];
            leafletMap.setView(coordinates, 13);
            userMarker = L.marker(coordinates).addTo(leafletMap);
        }, () => {
            userStore.storeGeolocationAnswered(true);
        });
    } else {
        userStore.storeGeolocationAnswered(true);
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('fullscreenchange', updateFullscreenButton);
    if (document.fullscreenElement === mapElement.value) {
        document.exitFullscreen?.();
    }
    mapElement.value?.classList.remove('map-fullscreen');
    leafletMap?.remove();
});
</script>

<style scoped>
#map {
    height: 75vh;
    margin: auto;
}

#map:fullscreen {
    width: 100vw;
    height: 100vh;
    margin: 0;
    background: white;
}

#map.map-fullscreen {
    position: fixed;
    inset: 0;
    z-index: 9999;
    width: 100vw;
    height: 100vh;
    margin: 0;
    background: white;
}

:global(.map-fullscreen-button) {
    width: 34px;
    height: 34px;
    padding: 0;
    background: white;
    color: #222;
    font-size: 16px;
    cursor: pointer;
}

:global(.map-fullscreen-button:hover) {
    background: #f4f4f4;
    color: #1c6e8c;
}
</style>
