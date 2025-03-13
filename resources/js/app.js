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
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

// Import Leaflet Control Geocoder
import * as Geocoder from 'leaflet-control-geocoder';
import 'leaflet-control-geocoder/dist/Control.Geocoder.css';
window.LeafletGeocoder = Geocoder;

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

    // Ensure MarkerClusterGroup is properly attached to L
    if (typeof window.L.MarkerClusterGroup === 'function' && typeof window.L.markerClusterGroup !== 'function') {
        window.L.markerClusterGroup = function(options) {
            return new window.L.MarkerClusterGroup(options);
        };
        console.log('MarkerClusterGroup initialized successfully');
    } else if (typeof window.L.markerClusterGroup !== 'function') {
        console.error('MarkerClusterGroup not available. Map clustering will not work.');
        // Create a fallback function to prevent errors
        window.L.markerClusterGroup = function(options) {
            console.warn('Using fallback layer group instead of marker cluster');
            return window.L.layerGroup();
        };
    }
});
