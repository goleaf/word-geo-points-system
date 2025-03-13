/**
 * Geocoding functionality for geo points
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize geocoding buttons
    initGeocodingButtons();

    // Initialize location data modal
    initLocationDataModal();
});

/**
 * Initialize geocoding buttons
 */
function initGeocodingButtons() {
    const geocodingButtons = document.querySelectorAll('.geocode-button');

    geocodingButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const geoPointId = this.dataset.geoPointId;
            const locale = this.dataset.locale;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Geocoding...';
            this.disabled = true;

            // Send geocoding request
            geocodeLocation(geoPointId, locale)
                .then(response => {
                    if (response.success) {
                        // Show success message with coordinates
                        Swal.fire({
                            title: 'Success!',
                            html: `Coordinates updated successfully:<br>Latitude: ${response.coordinates.lat}<br>Longitude: ${response.coordinates.long}`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Reload the page or redirect
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        throw new Error(response.error || 'Geocoding failed');
                    }
                })
                .catch(error => {
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred during geocoding',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                    // Reset button
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
        });
    });
}

/**
 * Initialize location data modal
 */
function initLocationDataModal() {
    // Get modal elements
    const locationModal = document.getElementById('locationDataModal');

    if (!locationModal) return;

    const locationForm = document.getElementById('locationDataForm');
    const generateButton = document.getElementById('generateLocationData');
    const applyButton = document.getElementById('applyLocationData');

    // Handle form submission
    locationForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        generateButton.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Generating...';
        generateButton.disabled = true;
        applyButton.disabled = true;

        // Get form data
        const formData = new FormData(locationForm);
        const data = {
            place_name: formData.get('place_name'),
            address: formData.get('address'),
            city_id: formData.get('city_id'),
            locale: formData.get('locale')
        };

        // Send request to generate location data
        generateLocationData(data)
            .then(response => {
                if (response.success) {
                    // Enable apply button
                    applyButton.disabled = false;

                    // Store data in the modal
                    locationModal.dataset.locationData = JSON.stringify(response.data);

                    // Show preview
                    document.getElementById('previewName').textContent = response.data.name;
                    document.getElementById('previewLat').textContent = response.data.coordinates.lat;
                    document.getElementById('previewLong').textContent = response.data.coordinates.long;
                    document.getElementById('previewDescription').textContent = response.data.description;

                    // Show preview section
                    document.getElementById('locationDataPreview').classList.remove('d-none');

                    // Dispatch event for map initialization
                    document.dispatchEvent(new CustomEvent('locationDataGenerated', {
                        detail: response.data
                    }));

                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'Location data generated successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    throw new Error(response.error || 'Failed to generate location data');
                }
            })
            .catch(error => {
                // Show error message
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An error occurred while generating location data',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Reset button
                generateButton.innerHTML = '<i class="bi bi-magic"></i> Generate Data';
                generateButton.disabled = false;
            });
    });

    // Handle apply button click
    applyButton.addEventListener('click', function() {
        const locationData = JSON.parse(locationModal.dataset.locationData || '{}');

        // Apply data to form fields
        if (locationData.name) {
            const currentLocale = document.getElementById('currentLocale').value;
            document.getElementById(`name_${currentLocale}`).value = locationData.name;
        }

        if (locationData.coordinates) {
            document.getElementById('lat').value = locationData.coordinates.lat;
            document.getElementById('long').value = locationData.coordinates.long;
        }

        if (locationData.description) {
            const currentLocale = document.getElementById('currentLocale').value;
            document.getElementById(`description_${currentLocale}`).value = locationData.description;
        }

        // Close modal
        const modalInstance = bootstrap.Modal.getInstance(locationModal);
        modalInstance.hide();

        // Show success message
        Swal.fire({
            title: 'Success!',
            text: 'Location data applied to form',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });

    // Reset form and preview when modal is hidden
    locationModal.addEventListener('hidden.bs.modal', function() {
        locationForm.reset();
        document.getElementById('locationDataPreview').classList.add('d-none');
        applyButton.disabled = true;
        locationModal.dataset.locationData = '';
    });
}

/**
 * Send geocoding request to the server
 *
 * @param {number} geoPointId - ID of the geo point
 * @param {string} locale - Language code for the name to use
 * @returns {Promise} - Promise with the response
 */
async function geocodeLocation(geoPointId, locale) {
    try {
        const response = await fetch('/geocode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                geo_point_id: geoPointId,
                locale: locale
            })
        });

        return await response.json();
    } catch (error) {
        console.error('Geocoding error:', error);
        throw new Error('Failed to send geocoding request');
    }
}

/**
 * Generate location data from place name and address
 *
 * @param {Object} data - Object containing place_name, address, city_id, and locale
 * @returns {Promise} - Promise with the response
 */
async function generateLocationData(data) {
    try {
        const response = await fetch('/generate-location-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        return await response.json();
    } catch (error) {
        console.error('Location data generation error:', error);
        throw new Error('Failed to generate location data');
    }
}
