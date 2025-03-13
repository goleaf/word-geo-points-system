/**
 * Map Integration with OpenStreetMap using Leaflet
 */

// Set custom icon paths for Leaflet
window.L.Icon.Default.prototype.options.iconUrl = '/img/marker-icon.png';
window.L.Icon.Default.prototype.options.shadowUrl = '/img/marker-shadow.png';
window.L.Icon.Default.prototype.options.iconRetinaUrl = '/img/marker-icon.png';

// Initialize a single geo point map with zoom level 14
window.initSingleGeoPointMap = function(elementId, lat, lng, name, description, cityName, countryName) {
    try {
        // Create map with initial view
        const map = window.mapService.createMap(elementId, {
            center: [lat, lng],
            zoom: 14
        });

        if (!map) return null;

        // Add marker for the geo point
        const marker = window.mapService.addMarker(map, lat, lng);

        // Add popup with information if provided
        if (name) {
            let popupContent = `<strong>${name}</strong>`;

            if (description) {
                popupContent += `<br><em>${description}</em>`;
            }

            if (cityName && countryName) {
                popupContent += `<br><small>${cityName}, ${countryName}</small>`;
            }

            popupContent += `<br><small class="text-muted">Coordinates: ${lat}, ${lng}</small>`;

            window.mapService.addPopup(marker, popupContent, true);
        }

        return map;
    } catch (error) {
        console.error('Error initializing single geo point map:', error);
        window.mapService.showMapError(elementId, 'Failed to initialize map. Please try refreshing the page.');
        return null;
    }
};

// Initialize a multi-point map for cities or countries
window.initMultiPointMap = function(elementId, points) {
    try {
        // Create map without initial view (will be set based on points)
        const map = window.mapService.createMap(elementId);

        if (!map) return null;

        // If no points, show message and return
        if (!points || points.length === 0) {
            window.mapService.showMapError(elementId, 'No geo points available to display on the map.');
            return null;
        }

        // Create bounds to fit all markers
        const bounds = window.L.latLngBounds();

        // Create a marker cluster group
        const markers = window.mapService.createMarkerGroup();

        // Show loading indicator
        const loadingElement = document.getElementById(`${elementId}-loading`);
        if (loadingElement) {
            loadingElement.style.display = 'flex';
        }

        // Create popup content function
        const createPopupContent = (point) => {
            if (!point.name) return null;

            let popupContent = `<strong>${point.name}</strong>`;

            if (point.description) {
                popupContent += `<br><em>${point.description}</em>`;
            }

            if (point.cityName) {
                popupContent += `<br><small>${point.cityName}`;
                if (point.countryName) {
                    popupContent += `, ${point.countryName}`;
                }
                popupContent += `</small>`;
            }

            if (point.url) {
                popupContent += `<br><a href="${point.url}" class="btn btn-sm btn-primary mt-2">View Details</a>`;
            }

            return popupContent;
        };

        // Add markers in chunks for better performance
        window.mapService.addMarkersInChunks(map, markers, points, createPopupContent, bounds);

        // Listen for markersloaded event
        map.once('markersloaded', () => {
            // Fit map to bounds with padding
            window.mapService.fitMapToBounds(map, bounds);

            // Hide loading indicator
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        });

        return map;
    } catch (error) {
        console.error('Error initializing multi-point map:', error);
        window.mapService.showMapError(elementId, 'Failed to initialize map. Please try refreshing the page.');
        return null;
    }
};

// Group points by city and display on map
window.initCityGroupedMap = function(elementId, data) {
    try {
        // Create map without initial view (will be set based on points)
        const map = window.mapService.createMap(elementId);

        if (!map) return null;

        // Check what type of data we're dealing with
        const isCountriesView = data.length > 0 && data[0].hasOwnProperty('cities');
        const isCitiesView = data.length > 0 && data[0].hasOwnProperty('geoPointsCount') && !data[0].hasOwnProperty('points');
        const isGeoPointsView = data.length > 0 && data[0].hasOwnProperty('points');

        // If no data, show message and return
        if (!data || data.length === 0) {
            window.mapService.showMapError(elementId, 'No data available to display on the map.');
            return null;
        }

        // Create bounds to fit all markers
        const bounds = window.L.latLngBounds();
        let hasPoints = false;

        // Create a marker cluster group for city markers
        const cityMarkers = window.mapService.createMarkerGroup({
            maxClusterRadius: 80,
            iconCreateFunction: function(cluster) {
                const count = cluster.getChildCount();
                return window.L.divIcon({
                    html: `<div class="cluster-marker">${count} Cities</div>`,
                    className: 'custom-cluster-marker',
                    iconSize: window.L.point(60, 40)
                });
            }
        });

        if (isCountriesView) {
            // Handle countries with cities
            data.forEach(country => {
                try {
                    if (!country.cities || country.cities.length === 0) {
                        return; // Skip countries with no cities
                    }

                    // Process each city in the country
                    country.cities.forEach(city => {
                        try {
                            if (!city.geoPointsCount || city.geoPointsCount === 0) {
                                return; // Skip cities with no geo points
                            }

                            hasPoints = true;

                            // Since we don't have actual coordinates in the countries view,
                            // we'll use random coordinates within a reasonable range for demonstration
                            // In a real app, you would store and use actual coordinates for countries/cities
                            const lat = 20 + Math.random() * 40; // Random lat between 20 and 60
                            const lng = -30 + Math.random() * 60; // Random lng between -30 and 30

                            const marker = window.L.marker([lat, lng]);

                            // Add popup with city information
                            let popupContent = `<strong>${city.name}</strong>`;
                            popupContent += `<br><small>Country: ${country.name}</small>`;
                            popupContent += `<br><small>Geo Points: ${city.geoPointsCount}</small>`;

                            if (city.url) {
                                popupContent += `<br><a href="${city.url}" class="btn btn-sm btn-primary mt-2">View City</a>`;
                            }

                            window.mapService.addPopup(marker, popupContent);

                            // Add to cluster group
                            cityMarkers.addLayer(marker);

                            // Add to bounds
                            bounds.extend([lat, lng]);

                            // Add city label
                            window.L.marker([lat, lng], {
                                icon: window.L.divIcon({
                                    className: 'city-label',
                                    html: `<div class="bg-primary text-white px-2 py-1 rounded shadow-sm">${city.name} (${city.geoPointsCount})</div>`,
                                    iconSize: [100, 20],
                                    iconAnchor: [50, 10]
                                })
                            }).addTo(map);
                        } catch (cityError) {
                            console.error('Error processing city:', cityError);
                        }
                    });
                } catch (countryError) {
                    console.error('Error processing country:', countryError);
                }
            });
        } else if (isCitiesView) {
            // Handle cities with geoPointsCount
            data.forEach(city => {
                try {
                    if (!city.geoPointsCount || city.geoPointsCount === 0) {
                        return; // Skip cities with no geo points
                    }

                    hasPoints = true;

                    // Since we don't have actual coordinates in the cities view,
                    // we'll use random coordinates within a reasonable range for demonstration
                    // In a real app, you would store and use actual coordinates for cities
                    const lat = 20 + Math.random() * 40; // Random lat between 20 and 60
                    const lng = -30 + Math.random() * 60; // Random lng between -30 and 30

                    const marker = window.L.marker([lat, lng]);

                    // Add popup with city information
                    let popupContent = `<strong>${city.name}</strong>`;
                    if (city.countryName) {
                        popupContent += `<br><small>Country: ${city.countryName}</small>`;
                    }
                    popupContent += `<br><small>Geo Points: ${city.geoPointsCount}</small>`;

                    if (city.url) {
                        popupContent += `<br><a href="${city.url}" class="btn btn-sm btn-primary mt-2">View City</a>`;
                    }

                    window.mapService.addPopup(marker, popupContent);

                    // Add to cluster group
                    cityMarkers.addLayer(marker);

                    // Add to bounds
                    bounds.extend([lat, lng]);

                    // Add city label
                    window.L.marker([lat, lng], {
                        icon: window.L.divIcon({
                            className: 'city-label',
                            html: `<div class="bg-primary text-white px-2 py-1 rounded shadow-sm">${city.name} (${city.geoPointsCount})</div>`,
                            iconSize: [100, 20],
                            iconAnchor: [50, 10]
                        })
                    }).addTo(map);
                } catch (cityError) {
                    console.error('Error processing city:', cityError);
                }
            });
        } else {
            // Original code for cities with geo points
            data.forEach(city => {
                try {
                    if (!city.points || city.points.length === 0) {
                        return; // Skip cities with no points
                    }

                    hasPoints = true;

                    // Create a marker cluster group for this city's points
                    const pointsCluster = window.mapService.createMarkerGroup({
                        maxClusterRadius: 50
                    });

                    // Add markers for all geo points in this city
                    city.points.forEach(point => {
                        try {
                            const marker = window.L.marker([point.lat, point.lng]);

                            // Add popup with information if provided
                            if (point.name) {
                                let popupContent = `<strong>${point.name}</strong>`;

                                if (point.description) {
                                    popupContent += `<br><em>${point.description}</em>`;
                                }

                                popupContent += `<br><small>${city.name}`;
                                if (city.countryName) {
                                    popupContent += `, ${city.countryName}`;
                                }
                                popupContent += `</small>`;

                                if (point.url) {
                                    popupContent += `<br><a href="${point.url}" class="btn btn-sm btn-primary mt-2">View Details</a>`;
                                }

                                window.mapService.addPopup(marker, popupContent);
                            }

                            // Add to cluster group
                            pointsCluster.addLayer(marker);

                            // Add to bounds
                            bounds.extend([point.lat, point.lng]);
                        } catch (pointError) {
                            console.error('Error processing point:', pointError);
                        }
                    });

                    // Add the cluster group to the map
                    map.addLayer(pointsCluster);

                    // Calculate the center of all points in this city
                    const cityCenter = pointsCluster.getBounds().getCenter();

                    // Add city label at the center of its points
                    window.L.marker([cityCenter.lat, cityCenter.lng], {
                        icon: window.L.divIcon({
                            className: 'city-label',
                            html: `<div class="bg-primary text-white px-2 py-1 rounded shadow-sm">${city.name} (${city.points.length})</div>`,
                            iconSize: [100, 20],
                            iconAnchor: [50, 10]
                        })
                    }).addTo(map);
                } catch (cityError) {
                    console.error('Error processing city:', cityError);
                }
            });
        }

        // Add the city markers cluster group to the map if we're in countries or cities view
        if (isCountriesView || isCitiesView) {
            map.addLayer(cityMarkers);
        }

        if (hasPoints) {
            // Fit map to bounds with padding
            window.mapService.fitMapToBounds(map, bounds);
        } else {
            // If no points, show a default view
            map.setView([40, 0], 2);
            window.mapService.showMapError(elementId, 'No geo points available to display on the map.');
        }

        return map;
    } catch (error) {
        console.error('Error initializing city grouped map:', error);
        window.mapService.showMapError(elementId, 'Failed to initialize map. Please try refreshing the page.');
        return null;
    }
};
