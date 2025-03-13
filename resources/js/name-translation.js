/**
 * Name translation functionality for entities
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize name translation buttons
    initNameTranslationButtons();

    // Initialize translate all names buttons
    initTranslateAllNamesButtons();
});

/**
 * Initialize name translation buttons
 */
function initNameTranslationButtons() {
    const nameTranslationButtons = document.querySelectorAll('.translate-name-button');

    nameTranslationButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const entityType = this.dataset.entityType;
            const entityId = this.dataset.entityId;
            const sourceLocale = this.dataset.sourceLocale;
            const targetLocale = this.dataset.targetLocale;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Translating...';
            this.disabled = true;

            // Send name translation request
            translateName(entityType, entityId, sourceLocale, targetLocale)
                .then(response => {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: 'Name translation completed successfully',
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
                        throw new Error(response.error || 'Name translation failed');
                    }
                })
                .catch(error => {
                    // Show error message
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred during name translation',
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
 * Initialize translate all names buttons
 */
function initTranslateAllNamesButtons() {
    const translateAllNamesButtons = document.querySelectorAll('.translate-all-names-button');

    translateAllNamesButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const entityType = this.dataset.entityType;
            const entityId = this.dataset.entityId;
            const sourceLocale = this.dataset.sourceLocale;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Translating All...';
            this.disabled = true;

            // Confirm before proceeding
            Swal.fire({
                title: 'Translate to All Languages?',
                text: 'This will translate the name to all available languages. Continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, translate all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send translate all names request
                    translateAllNames(entityType, entityId, sourceLocale)
                        .then(response => {
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
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
                                throw new Error(response.error || 'Translation failed');
                            }
                        })
                        .catch(error => {
                            // Show error message
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'An error occurred during translation',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });

                            // Reset button
                            this.innerHTML = originalText;
                            this.disabled = false;
                        });
                } else {
                    // Reset button if canceled
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            });
        });
    });
}

/**
 * Send name translation request to the server
 *
 * @param {string} entityType - Type of entity (country, city, geo_point)
 * @param {number} entityId - ID of the entity
 * @param {string} sourceLocale - Source language code
 * @param {string} targetLocale - Target language code
 * @returns {Promise} - Promise with the response
 */
async function translateName(entityType, entityId, sourceLocale, targetLocale) {
    try {
        const response = await fetch('/translate-name', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                entity_type: entityType,
                entity_id: entityId,
                source_locale: sourceLocale,
                target_locale: targetLocale
            })
        });

        return await response.json();
    } catch (error) {
        console.error('Name translation error:', error);
        throw new Error('Failed to send name translation request');
    }
}

/**
 * Send translate all names request to the server
 *
 * @param {string} entityType - Type of entity (country, city, geo_point)
 * @param {number} entityId - ID of the entity
 * @param {string} sourceLocale - Source language code
 * @returns {Promise} - Promise with the response
 */
async function translateAllNames(entityType, entityId, sourceLocale) {
    try {
        const response = await fetch('/translate-all-names', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                entity_type: entityType,
                entity_id: entityId,
                source_locale: sourceLocale
            })
        });

        return await response.json();
    } catch (error) {
        console.error('Translate all names error:', error);
        throw new Error('Failed to send translate all names request');
    }
}
