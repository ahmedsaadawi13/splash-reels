// FILE: /public/assets/js/app.js

// SplashReels Application JavaScript

(function() {
    'use strict';

    // Get CSRF token
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // AJAX helper
    window.ajax = function(url, options) {
        options = options || {};
        const method = options.method || 'GET';
        const data = options.data || null;
        const headers = options.headers || {};

        // Add CSRF token for non-GET requests
        if (method !== 'GET') {
            headers['X-CSRF-Token'] = getCsrfToken();
        }

        const fetchOptions = {
            method: method,
            headers: headers
        };

        if (data && method !== 'GET') {
            if (data instanceof FormData) {
                fetchOptions.body = data;
            } else {
                headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(data);
            }
        }

        return fetch(url, fetchOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            });
    };

    // Confirm delete
    window.confirmDelete = function(message) {
        return confirm(message || 'Are you sure you want to delete this item?');
    };

    // Generate AI Highlights
    window.generateHighlights = function(mediaFileId) {
        const button = event.target;
        button.disabled = true;
        button.textContent = 'Generating...';

        ajax('/media/' + mediaFileId + '/generate-clips', {
            method: 'POST'
        })
        .then(function(response) {
            if (response.success) {
                alert('Generated ' + response.count + ' clip suggestions!');
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
                button.disabled = false;
                button.textContent = 'Generate AI Highlights';
            }
        })
        .catch(function(error) {
            alert('Error generating highlights');
            button.disabled = false;
            button.textContent = 'Generate AI Highlights';
        });
    };

    // Accept clip suggestion
    window.acceptSuggestion = function(suggestionId) {
        const templateId = document.getElementById('template_' + suggestionId).value;
        const platformHint = document.getElementById('platform_' + suggestionId).value;

        const formData = new FormData();
        formData.append('csrf_token', getCsrfToken());
        formData.append('template_id', templateId);
        formData.append('platform_hint', platformHint);

        ajax('/clips/accept-suggestion/' + suggestionId, {
            method: 'POST',
            data: formData
        })
        .then(function(response) {
            if (response.success) {
                window.location.href = response.redirect;
            } else {
                alert('Error: ' + response.message);
            }
        })
        .catch(function(error) {
            alert('Error accepting suggestion');
        });
    };

    // Render clip
    window.renderClip = function(clipId) {
        const button = event.target;
        button.disabled = true;
        button.textContent = 'Rendering...';

        const formData = new FormData();
        formData.append('csrf_token', getCsrfToken());

        ajax('/clips/' + clipId + '/render', {
            method: 'POST',
            data: formData
        })
        .then(function(response) {
            if (response.success) {
                alert('Clip rendered successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
                button.disabled = false;
                button.textContent = 'Render Clip';
            }
        })
        .catch(function(error) {
            alert('Error rendering clip');
            button.disabled = false;
            button.textContent = 'Render Clip';
        });
    };

    // Export clip
    window.exportClip = function(clipId) {
        const button = event.target;
        button.disabled = true;
        button.textContent = 'Exporting...';

        const formData = new FormData();
        formData.append('csrf_token', getCsrfToken());

        ajax('/clips/' + clipId + '/export', {
            method: 'POST',
            data: formData
        })
        .then(function(response) {
            if (response.success) {
                alert('Export ready! Download link: ' + response.download_url);
                window.location.href = response.download_url;
                button.disabled = false;
                button.textContent = 'Export';
            } else {
                alert('Error: ' + response.message);
                button.disabled = false;
                button.textContent = 'Export';
            }
        })
        .catch(function(error) {
            alert('Error exporting clip');
            button.disabled = false;
            button.textContent = 'Export';
        });
    };

    // Toggle file upload method
    window.toggleUploadMethod = function() {
        const sourceType = document.querySelector('input[name="source_type"]:checked').value;
        const fileUpload = document.getElementById('file-upload-section');
        const urlUpload = document.getElementById('url-upload-section');

        if (sourceType === 'upload') {
            fileUpload.style.display = 'block';
            urlUpload.style.display = 'none';
        } else {
            fileUpload.style.display = 'none';
            urlUpload.style.display = 'block';
        }
    };

    // Initialize on DOM load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('SplashReels initialized');

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    });

})();
