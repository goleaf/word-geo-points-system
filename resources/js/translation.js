/**
 * Translation functionality for entity descriptions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize translation buttons
    initTranslationButtons();
});

/**
 * Initialize translation buttons
 */
function initTranslationButtons() {
    const translationButtons = document.querySelectorAll('.translate-button');

    translationButtons.forEach(button => {
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

            // Send translation request
            translateDescription(entityType, entityId, sourceLocale, targetLocale)
                .then(response => {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: 'Translation completed successfully',
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
        });
    });
}

/**
 * Send translation request to the server
 *
 * @param {string} entityType - Type of entity (country, city, geo_point)
 * @param {number} entityId - ID of the entity
 * @param {string} sourceLocale - Source language code
 * @param {string} targetLocale - Target language code
 * @returns {Promise} - Promise with the response
 */
async function translateDescription(entityType, entityId, sourceLocale, targetLocale) {
    try {
        const response = await fetch('/translate', {
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
        console.error('Translation error:', error);
        throw new Error('Failed to send translation request');
    }
}
