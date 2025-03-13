/**
 * Map Service - A modular approach to handle map functionality
 */

class MapService {
    /**
     * Initialize the map service
     */
    constructor() {
        this.maps = new Map(); // Store map instances
        this.defaultTileLayer = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        this.defaultAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        // Set custom icon paths for Leaflet
        if (window.L && window.L.Icon && window.L.Icon.Default && window.L.Icon.Default.prototype) {
            window.L.Icon.Default.prototype.options.iconUrl = '/img/marker-icon.png';
            window.L.Icon.Default.prototype.options.shadowUrl = '/img/marker-shadow.png';
            window.L.Icon.Default.prototype.options.iconRetinaUrl = '/img/marker-icon.png';
        }

        // Check if MarkerClusterGroup is available
        this.hasMarkerCluster = typeof window.L.markerClusterGroup === 'function';
    }

    /**
     * Create a new map instance
     *
     * @param {string} elementId - ID of the HTML element to contain the map
     * @param {Object} options - Map options
     * @returns {Object} - Leaflet map instance
     */
    createMap(elementId, options = {}) {
        try {
            const element = document.getElementById(elementId);
            if (!element) {
                console.error(`Map element with ID "${elementId}" not found`);
                return null;
            }

            // Create map with default options
            const defaultOptions = {
                zoomControl: true,
                attributionControl: true,
                scrollWheelZoom: true
            };

            const map = window.L.map(elementId, { ...defaultOptions, ...options });

            // Add tile layer
            this.addTileLayer(map);

            // Add geocoder control
            this.addGeocoderControl(map);

            // Store map instance
            this.maps.set(elementId, map);

            // Add responsive handling
            this.handleResponsive(map);

            // Add accessibility features
            this.enhanceAccessibility(element);

            return map;
        } catch (error) {
            console.error('Error creating map:', error);
            this.showMapError(elementId, 'Failed to initialize map. Please try refreshing the page.');
            return null;
        }
    }

    /**
     * Add the default tile layer to a map
     *
     * @param {Object} map - Leaflet map instance
     * @returns {Object} - Tile layer instance
     */
    addTileLayer(map) {
        try {
            return window.L.tileLayer(this.defaultTileLayer, {
                attribution: this.defaultAttribution,
                maxZoom: 19
            }).addTo(map);
        } catch (error) {
            console.error('Error adding tile layer:', error);
            return null;
        }
    }

    /**
     * Add geocoder control to a map
     *
     * @param {Object} map - Leaflet map instance
     * @returns {Object} - Geocoder control instance
     */
    addGeocoderControl(map) {
        try {
            if (!window.L.Control.geocoder) {
                console.warn('Geocoder control not available');
                return null;
            }

            return window.L.Control.geocoder({
                defaultMarkGeocode: false,
                position: 'topleft',
                placeholder: 'Search for places...',
                errorMessage: 'Nothing found.',
                showResultIcons: true
            }).on('markgeocode', function(e) {
                const bbox = e.geocode.bbox;
                const poly = window.L.polygon([
                    bbox.getSouthEast(),
                    bbox.getNorthEast(),
                    bbox.getNorthWest(),
                    bbox.getSouthWest()
                ]).addTo(map);
                map.fitBounds(poly.getBounds());
                poly.remove();
            }).addTo(map);
        } catch (error) {
            console.error('Error adding geocoder control:', error);
            return null;
        }
    }

    /**
     * Create a marker cluster group or fallback to layer group
     *
     * @param {Object} options - Cluster options
     * @returns {Object} - Marker cluster group or layer group
     */
    createMarkerGroup(options = {}) {
        try {
            if (this.hasMarkerCluster) {
                return window.L.markerClusterGroup({
                    showCoverageOnHover: false,
                    maxClusterRadius: 50,
                    iconCreateFunction: function(cluster) {
                        const count = cluster.getChildCount();
                        return window.L.divIcon({
                            html: `<div class="cluster-marker">${count}</div>`,
                            className: 'custom-cluster-marker',
                            iconSize: window.L.point(40, 40)
                        });
                    },
                    ...options
                });
            } else {
                console.warn('Using layer group instead of marker cluster');
                return window.L.layerGroup();
            }
        } catch (error) {
            console.error('Error creating marker group:', error);
            return window.L.layerGroup(); // Fallback
        }
    }

    /**
     * Add a single marker to a map
     *
     * @param {Object} map - Leaflet map instance
     * @param {number} lat - Latitude
     * @param {number} lng - Longitude
     * @param {Object} options - Marker options
     * @returns {Object} - Marker instance
     */
    addMarker(map, lat, lng, options = {}) {
        try {
            return window.L.marker([lat, lng], options).addTo(map);
        } catch (error) {
            console.error('Error adding marker:', error);
            return null;
        }
    }

    /**
     * Create a popup for a marker
     *
     * @param {Object} marker - Leaflet marker instance
     * @param {string} content - HTML content for the popup
     * @param {boolean} openNow - Whether to open the popup immediately
     * @returns {Object} - Marker instance with popup
     */
    addPopup(marker, content, openNow = false) {
        try {
            if (openNow) {
                marker.bindPopup(content).openPopup();
            } else {
                marker.bindPopup(content);
            }
            return marker;
        } catch (error) {
            console.error('Error adding popup:', error);
            return marker;
        }
    }

    /**
     * Fit map bounds to include all markers
     *
     * @param {Object} map - Leaflet map instance
     * @param {Object} bounds - Leaflet bounds object
     * @param {Object} options - Options for fitting bounds
     */
    fitMapToBounds(map, bounds, options = { padding: [50, 50] }) {
        try {
            if (bounds.isValid()) {
                map.fitBounds(bounds, options);
            } else {
                console.warn('Invalid bounds, setting default view');
                map.setView([40, 0], 2);
            }
        } catch (error) {
            console.error('Error fitting map to bounds:', error);
            map.setView([40, 0], 2); // Fallback to default view
        }
    }

    /**
     * Show an error message in the map container
     *
     * @param {string} elementId - ID of the HTML element
     * @param {string} message - Error message to display
     */
    showMapError(elementId, message) {
        try {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        ${message}
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error showing map error:', error);
        }
    }

    /**
     * Handle responsive behavior for the map
     *
     * @param {Object} map - Leaflet map instance
     */
    handleResponsive(map) {
        try {
            const resizeObserver = new ResizeObserver(entries => {
                for (const entry of entries) {
                    if (entry.target.id && this.maps.has(entry.target.id)) {
                        map.invalidateSize();
                    }
                }
            });

            const element = map.getContainer();
            if (element) {
                resizeObserver.observe(element);
            }

            // Store observer reference for cleanup
            map._resizeObserver = resizeObserver;
        } catch (error) {
            console.error('Error setting up responsive handling:', error);
        }
    }

    /**
     * Enhance accessibility for the map
     *
     * @param {HTMLElement} element - Map container element
     */
    enhanceAccessibility(element) {
        try {
            // Add appropriate ARIA attributes
            element.setAttribute('role', 'application');
            element.setAttribute('aria-label', 'Interactive map');

            // Add a screen reader description
            const srDescription = document.createElement('span');
            srDescription.className = 'sr-only';
            srDescription.textContent = 'This is an interactive map. Use keyboard controls to navigate.';
            element.appendChild(srDescription);
        } catch (error) {
            console.error('Error enhancing accessibility:', error);
        }
    }

    /**
     * Clean up a map instance
     *
     * @param {string} elementId - ID of the HTML element
     */
    destroyMap(elementId) {
        try {
            const map = this.maps.get(elementId);
            if (map) {
                // Clean up resize observer if it exists
                if (map._resizeObserver) {
                    map._resizeObserver.disconnect();
                }

                map.remove();
                this.maps.delete(elementId);
            }
        } catch (error) {
            console.error('Error destroying map:', error);
        }
    }

    /**
     * Add multiple markers efficiently using chunking for better performance
     *
     * @param {Object} map - Leaflet map instance
     * @param {Object} markerGroup - Marker group or cluster to add markers to
     * @param {Array} points - Array of points with lat and lng properties
     * @param {Function} createPopupContent - Function to create popup content for each marker
     * @param {Object} bounds - Leaflet bounds object to extend
     * @param {number} chunkSize - Number of markers to process in each chunk
     */
    addMarkersInChunks(map, markerGroup, points, createPopupContent, bounds, chunkSize = 50) {
        if (!points || points.length === 0) return;

        const totalPoints = points.length;
        let processedCount = 0;

        // Process markers in chunks for better performance
        const processChunk = (startIndex) => {
            const endIndex = Math.min(startIndex + chunkSize, totalPoints);

            for (let i = startIndex; i < endIndex; i++) {
                try {
                    const point = points[i];
                    if (!point || typeof point.lat === 'undefined' || typeof point.lng === 'undefined') {
                        console.warn('Invalid point data:', point);
                        continue;
                    }

                    const marker = window.L.marker([point.lat, point.lng]);

                    // Add popup if createPopupContent function is provided
                    if (typeof createPopupContent === 'function') {
                        const content = createPopupContent(point);
                        if (content) {
                            this.addPopup(marker, content);
                        }
                    }

                    // Add to marker group
                    markerGroup.addLayer(marker);

                    // Extend bounds
                    if (bounds) {
                        bounds.extend([point.lat, point.lng]);
                    }

                    processedCount++;
                } catch (error) {
                    console.error('Error processing marker:', error);
                }
            }

            // If there are more points to process, schedule the next chunk
            if (endIndex < totalPoints) {
                setTimeout(() => processChunk(endIndex), 0);
            } else {
                // All points processed, add the marker group to the map
                map.addLayer(markerGroup);

                // Dispatch event to notify that all markers have been added
                map.fire('markersloaded', { count: processedCount });

                // Update loading indicator if it exists
                const mapId = map.getContainer().id;
                const loadingElement = document.getElementById(`${mapId}-loading`);
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
            }
        };

        // Start processing the first chunk
        processChunk(0);
    }
}

// Create and export a singleton instance
const mapService = new MapService();
export default mapService;

// Also make it available globally
window.mapService = mapService;
