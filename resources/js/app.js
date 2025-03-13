import './bootstrap';

// Import Bootstrap JS
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Import Leaflet
import * as L from 'leaflet';
import 'leaflet/dist/leaflet.css';
window.L = L;

// Import Leaflet MarkerCluster
import * as MarkerCluster from 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

// Import Leaflet Control Geocoder
import * as Geocoder from 'leaflet-control-geocoder';
import 'leaflet-control-geocoder/dist/Control.Geocoder.css';
window.LeafletGeocoder = Geocoder;

// Set custom icon paths for Leaflet
if (window.L && window.L.Icon && window.L.Icon.Default) {
    window.L.Icon.Default.prototype.options.iconUrl = '/img/marker-icon.png';
    window.L.Icon.Default.prototype.options.shadowUrl = '/img/marker-shadow.png';
    window.L.Icon.Default.prototype.options.iconRetinaUrl = '/img/marker-icon.png';
}

// Ensure MarkerClusterGroup is available
if (!window.L.MarkerClusterGroup && MarkerCluster) {
    console.log('Adding MarkerClusterGroup to Leaflet');
    // This is a workaround for when the MarkerClusterGroup is not automatically attached to L
    window.L.MarkerClusterGroup = MarkerCluster.MarkerClusterGroup || function(options) {
        console.warn('Using fallback layerGroup instead of MarkerClusterGroup');
        return window.L.layerGroup();
    };
}

// Check if MarkerClusterGroup is available
const hasMarkerCluster = window.L && typeof window.L.MarkerClusterGroup === 'function';
console.log('MarkerClusterGroup available:', hasMarkerCluster);

// Create a wrapper for Leaflet functionality
window.LeafletWrapper = {
    // Original Leaflet functions
    map: function(elementId, options) {
        return window.L.map(elementId, options);
    },
    tileLayer: function(url, options) {
        return window.L.tileLayer(url, options);
    },
    marker: function(latlng, options) {
        return window.L.marker(latlng, options);
    },
    latLngBounds: function() {
        return window.L.latLngBounds();
    },
    layerGroup: function() {
        return window.L.layerGroup();
    },
    divIcon: function(options) {
        return window.L.divIcon(options);
    },
    point: function(x, y) {
        return window.L.point(x, y);
    },
    polygon: function(latlngs, options) {
        return window.L.polygon(latlngs, options);
    },

    // MarkerClusterGroup wrapper
    markerClusterGroup: function(options) {
        try {
            if (hasMarkerCluster) {
                return new window.L.MarkerClusterGroup(options);
            } else {
                console.warn('MarkerClusterGroup not available, using layerGroup instead');
                return window.L.layerGroup();
            }
        } catch (error) {
            console.error('Error creating marker cluster group:', error);
            return window.L.layerGroup(); // Fallback
        }
    }
};

// Import map service
import './map-service';

// Import custom scripts
import './delete-confirmation';
import './map-integration';
import './translation';
import './name-translation';
import './geocoding';

// Custom JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Enable Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Enable Bootstrap popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Log initialization status
    if (window.LeafletWrapper) {
        console.log('LeafletWrapper initialized successfully');
    }

    console.log('Leaflet and extensions initialized successfully');
});
