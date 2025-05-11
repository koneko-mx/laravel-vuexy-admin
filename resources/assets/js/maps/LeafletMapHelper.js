import '@vuexy-admin/assets/vendor/libs/leaflet/leaflet'
//import './../../vendor/libs/leaflet/leaflet'

export const LeafletMapHelper = (() => {
    let mapInstance, markerInstance;

    const DEFAULT_COORDS = [19.4326, -99.1332]; // Coordenadas de CDMX por defecto

    // Valida coordenadas
    const isValidCoordinate = (lat, lng) => {
        return lat && !isNaN(lat) && lat >= -90 && lat <= 90 && lat !== 0 &&
               lng && !isNaN(lng) && lng >= -180 && lng <= 180 && lng !== 0;
    };

    // Crea opciones del mapa según el modo
    const getMapOptions = (mode) => ({
        scrollWheelZoom: mode !== 'delete',
        dragging: mode !== 'delete',
        doubleClickZoom: mode !== 'delete',
        boxZoom: mode !== 'delete',
        keyboard: mode !== 'delete',
        zoomControl: mode !== 'delete',
        touchZoom: mode !== 'delete'
    });

    // Destruir el mapa existente
    const destroyMap = () => {
        if (mapInstance) {
            mapInstance.off();
            mapInstance.remove();
            mapInstance = null;
        }
        removeMarker();
    };

    // Crear marcador en el mapa
    const createMarker = (lat, lng, draggable = false, onDragEnd) => {
        if (isValidCoordinate(lat, lng)) {
            markerInstance = L.marker([lat, lng], { draggable }).addTo(mapInstance)
                .bindPopup('<b>Ubicación seleccionada</b>').openPopup();

            if (draggable && onDragEnd) {
                markerInstance.on('dragend', (e) => {
                    const { lat, lng } = e.target.getLatLng();
                    onDragEnd(lat, lng);
                });
            }
        }
    };

    // Eliminar marcador
    const removeMarker = () => {
        if (markerInstance) {
            markerInstance.remove();
            markerInstance = null;
        }
    };

    // Actualizar coordenadas en formulario
    const updateCoordinates = (lat, lng, latSelector, lngSelector, livewireInstance) => {
        const latInput = document.querySelector(latSelector);
        const lngInput = document.querySelector(lngSelector);

        if (!latInput || !lngInput) {
            console.warn(`⚠️ No se encontró el elemento del DOM para latitud (${latSelector}) o longitud (${lngSelector})`);
            return;
        }

        latInput.value = lat ? lat.toFixed(6) : '';
        lngInput.value = lng ? lng.toFixed(6) : '';

        if (livewireInstance) {
            livewireInstance.lat = lat ? lat.toFixed(6) : null;
            livewireInstance.lng = lng ? lng.toFixed(6) : null;
        }
    };

    // Inicializar el mapa
    const initializeMap = (locationInputs, mode = 'create', livewireInstance = null) => {
        const mapElement = document.getElementById(locationInputs.mapId);

        if (!mapElement) {
            console.error(`❌ No se encontró el contenedor del mapa con ID: ${locationInputs.mapId}`);
            return;
        }

        let latElement = document.querySelector(locationInputs.lat);
        let lngElement = document.querySelector(locationInputs.lng);

        if (!latElement || !lngElement) {
            console.error(`❌ No se encontraron los campos de latitud (${locationInputs.lat}) o longitud (${locationInputs.lng})`);
            return;
        }

        let lat = parseFloat(latElement.value);
        let lng = parseFloat(lngElement.value);

        const mapCoords = isValidCoordinate(lat, lng) ? [lat, lng] : DEFAULT_COORDS;
        const zoomLevel = isValidCoordinate(lat, lng) ? 16 : 13;

        if (mapInstance) destroyMap();

        mapInstance = L.map(locationInputs.mapId, getMapOptions(mode)).setView(mapCoords, zoomLevel);
        L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(mapInstance);

        if (mode !== 'create') createMarker(lat, lng, mode === 'edit', (lat, lng) => updateCoordinates(lat, lng, locationInputs.lat, locationInputs.lng, livewireInstance));

        if (mode !== 'delete') {
            mapInstance.on('click', (e) => {
                const { lat, lng } = e.latlng;
                removeMarker();
                createMarker(lat, lng, true, (lat, lng) => updateCoordinates(lat, lng, locationInputs.lat, locationInputs.lng, livewireInstance));
                updateCoordinates(lat, lng, locationInputs.lat, locationInputs.lng, livewireInstance);
            });
        }

        /*
        const btnClearElement = document.querySelector(locationInputs.btnClear);

        if(!btnClearElement){
            console.error(`❌ No se encontró el botón de limpiar con ID: ${locationInputs.btnClear}`);return;
        }
        */
    };

    return {
        initializeMap,
        clearCoordinates: () => {
            removeMarker();
        },
    };
})();

window.LeafletMapHelper = LeafletMapHelper;
