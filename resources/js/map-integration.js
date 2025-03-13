/**
 * Map Integration with OpenStreetMap using Leaflet
 */

// Set custom icon paths for Leaflet
L.Icon.Default.prototype.options.iconUrl = '/img/marker-icon.png';
L.Icon.Default.prototype.options.shadowUrl = '/img/marker-shadow.png';
L.Icon.Default.prototype.options.iconRetinaUrl = '/img/marker-icon.png';

// Initialize a single geo point map with zoom level 14
window.initSingleGeoPointMap = function(elementId, lat, lng, name, description, cityName, countryName) {
    const map = L.map(elementId).setView([lat, lng], 14);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add geocoder control
    L.Control.geocoder({
        defaultMarkGeocode: false,
        position: 'topleft',
        placeholder: 'Search for places...',
        errorMessage: 'Nothing found.',
        showResultIcons: true
    }).on('markgeocode', function(e) {
        const bbox = e.geocode.bbox;
        const poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]).addTo(map);
        map.fitBounds(poly.getBounds());
        poly.remove();
    }).addTo(map);

    // Add marker for the geo point
    const marker = L.marker([lat, lng]).addTo(map);

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

        marker.bindPopup(popupContent).openPopup();
    }

    return map;
};

// Initialize a multi-point map for cities or countries
window.initMultiPointMap = function(elementId, points) {
    // Create map without initial view (will be set based on points)
    const map = L.map(elementId);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add geocoder control
    L.Control.geocoder({
        defaultMarkGeocode: false,
        position: 'topleft',
        placeholder: 'Search for places...',
        errorMessage: 'Nothing found.',
        showResultIcons: true
    }).on('markgeocode', function(e) {
        const bbox = e.geocode.bbox;
        const poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]).addTo(map);
        map.fitBounds(poly.getBounds());
        poly.remove();
    }).addTo(map);

    // If no points, show message and return
    if (!points || points.length === 0) {
        document.getElementById(elementId).innerHTML =
            '<div class="alert alert-info">No geo points available to display on the map.</div>';
        return null;
    }

    // Create bounds to fit all markers
    const bounds = L.latLngBounds();

    // Create a marker cluster group
    const markers = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 50,
        iconCreateFunction: function(cluster) {
            const count = cluster.getChildCount();
            return L.divIcon({
                html: `<div class="cluster-marker">${count}</div>`,
                className: 'custom-cluster-marker',
                iconSize: L.point(40, 40)
            });
        }
    });

    // Add markers for all points
    points.forEach(point => {
        const marker = L.marker([point.lat, point.lng]);

        // Add popup with information if provided
        if (point.name) {
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

            marker.bindPopup(popupContent);
        }

        // Add to cluster group
        markers.addLayer(marker);

        // Add to bounds
        bounds.extend([point.lat, point.lng]);
    });

    // Add the marker cluster group to the map
    map.addLayer(markers);

    // Fit map to bounds with padding
    map.fitBounds(bounds, { padding: [50, 50] });

    return map;
};

// Group points by city and display on map
window.initCityGroupedMap = function(elementId, data) {
    // Create map without initial view (will be set based on points)
    const map = L.map(elementId);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add geocoder control
    L.Control.geocoder({
        defaultMarkGeocode: false,
        position: 'topleft',
        placeholder: 'Search for places...',
        errorMessage: 'Nothing found.',
        showResultIcons: true
    }).on('markgeocode', function(e) {
        const bbox = e.geocode.bbox;
        const poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]).addTo(map);
        map.fitBounds(poly.getBounds());
        poly.remove();
    }).addTo(map);

    // Check what type of data we're dealing with
    const isCountriesView = data.length > 0 && data[0].hasOwnProperty('cities');
    const isCitiesView = data.length > 0 && data[0].hasOwnProperty('geoPointsCount') && !data[0].hasOwnProperty('points');
    const isGeoPointsView = data.length > 0 && data[0].hasOwnProperty('points');

    // If no data, show message and return
    if (!data || data.length === 0) {
        document.getElementById(elementId).innerHTML =
            '<div class="alert alert-info">No data available to display on the map.</div>';
        return null;
    }

    // Create bounds to fit all markers
    const bounds = L.latLngBounds();
    let hasPoints = false;

    // Create a marker cluster group for city markers
    const cityMarkers = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 80,
        iconCreateFunction: function(cluster) {
            const count = cluster.getChildCount();
            return L.divIcon({
                html: `<div class="cluster-marker">${count} Cities</div>`,
                className: 'custom-cluster-marker',
                iconSize: L.point(60, 40)
            });
        }
    });

    if (isCountriesView) {
        // Handle countries with cities
        data.forEach(country => {
            if (!country.cities || country.cities.length === 0) {
                return; // Skip countries with no cities
            }

            // Process each city in the country
            country.cities.forEach(city => {
                if (!city.geoPointsCount || city.geoPointsCount === 0) {
                    return; // Skip cities with no geo points
                }

                hasPoints = true;

                // Since we don't have actual coordinates in the countries view,
                // we'll use random coordinates within a reasonable range for demonstration
                // In a real app, you would store and use actual coordinates for countries/cities
                const lat = 20 + Math.random() * 40; // Random lat between 20 and 60
                const lng = -30 + Math.random() * 60; // Random lng between -30 and 30

                const marker = L.marker([lat, lng]);

                // Add popup with city information
                let popupContent = `<strong>${city.name}</strong>`;
                popupContent += `<br><small>Country: ${country.name}</small>`;
                popupContent += `<br><small>Geo Points: ${city.geoPointsCount}</small>`;

                if (city.url) {
                    popupContent += `<br><a href="${city.url}" class="btn btn-sm btn-primary mt-2">View City</a>`;
                }

                marker.bindPopup(popupContent);

                // Add to cluster group
                cityMarkers.addLayer(marker);

                // Add to bounds
                bounds.extend([lat, lng]);

                // Add city label
                L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'city-label',
                        html: `<div class="bg-primary text-white px-2 py-1 rounded shadow-sm">${city.name} (${city.geoPointsCount})</div>`,
                        iconSize: [100, 20],
                        iconAnchor: [50, 10]
                    })
                }).addTo(map);
            });
        });
    } else if (isCitiesView) {
        // Handle cities with geoPointsCount
        data.forEach(city => {
            if (!city.geoPointsCount || city.geoPointsCount === 0) {
                return; // Skip cities with no geo points
            }

            hasPoints = true;

            // Since we don't have actual coordinates in the cities view,
            // we'll use random coordinates within a reasonable range for demonstration
            // In a real app, you would store and use actual coordinates for cities
            const lat = 20 + Math.random() * 40; // Random lat between 20 and 60
            const lng = -30 + Math.random() * 60; // Random lng between -30 and 30

            const marker = L.marker([lat, lng]);

            // Add popup with city information
            let popupContent = `<strong>${city.name}</strong>`;
            if (city.countryName) {
                popupContent += `<br><small>Country: ${city.countryName}</small>`;
            }
            popupContent += `<br><small>Geo Points: ${city.geoPointsCount}</small>`;

            if (city.url) {
                popupContent += `<br><a href="${city.url}" class="btn btn-sm btn-primary mt-2">View City</a>`;
            }

            marker.bindPopup(popupContent);

            // Add to cluster group
            cityMarkers.addLayer(marker);

            // Add to bounds
            bounds.extend([lat, lng]);

            // Add city label
            L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'city-label',
                    html: `<div class="bg-primary text-white px-2 py-1 rounded shadow-sm">${city.name} (${city.geoPointsCount})</div>`,
                    iconSize: [100, 20],
                    iconAnchor: [50, 10]
                })
            }).addTo(map);
        });
    } else {
        // Original code for cities with geo points
        data.forEach(city => {
            if (!city.points || city.points.length === 0) {
                return; // Skip cities with no points
            }

            hasPoints = true;

            // Create a marker cluster group for this city's points
            const pointsCluster = L.markerClusterGroup({
                showCoverageOnHover: false,
                maxClusterRadius: 50
            });

            // Add markers for all geo points in this city
            city.points.forEach(point => {
                const marker = L.marker([point.lat, point.lng]);

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

                    marker.bindPopup(popupContent);
                }

                // Add to cluster group
                pointsCluster.addLayer(marker);

                // Add to bounds
                bounds.extend([point.lat, point.lng]);
            });

            // Add the cluster group to the map
            map.addLayer(pointsCluster);

            // Calculate the center of all points in this city
            const cityCenter = pointsCluster.getBounds().getCenter();

            // Add city label at the center of its points
            L.marker([cityCenter.lat, cityCenter.lng], {
                icon: L.divIcon({
                    className: 'city-label',
                    html: `<div class="bg-primary text-white px-2 py-1 rounded shadow-sm">${city.name} (${city.points.length})</div>`,
                    iconSize: [100, 20],
                    iconAnchor: [50, 10]
                })
            }).addTo(map);
        });
    }

    // Add the city markers cluster group to the map if we're in countries or cities view
    if (isCountriesView || isCitiesView) {
        map.addLayer(cityMarkers);
    }

    if (hasPoints) {
        // Fit map to bounds with padding
        map.fitBounds(bounds, { padding: [50, 50] });
    } else {
        // If no points, show a default view
        map.setView([40, 0], 2);
        document.getElementById(elementId).innerHTML =
            '<div class="alert alert-info">No geo points available to display on the map.</div>';
    }

    return map;
};
