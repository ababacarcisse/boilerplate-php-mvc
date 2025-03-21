// Récupération de la constante BASE_URL depuis le serveur
let BASE_URL = '/gestion-pharmacie';

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                matricule: formData.get('matricule'),
                password: formData.get('password')
            };

            // Supprimer les alertes existantes
            removeExistingAlerts();

            // Afficher l'alerte de chargement
            showAlert('Connexion en cours...', 'info');

            fetch(BASE_URL + '/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                removeExistingAlerts();
                if (data.success) {
                    showAlert('Connexion réussie! Redirection...', 'success');
                    setTimeout(() => {
                        window.location.href = BASE_URL + '/';
                    }, 1500);
                } else {
                    showAlert(data.message || 'Identifiants invalides', 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                removeExistingAlerts();
                showAlert('Erreur de connexion au serveur', 'danger');
            });
        });
    }

    // Fonction pour supprimer les alertes existantes
    function removeExistingAlerts() {
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
    }

    // Fonction pour afficher une alerte
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';

        const icon = getAlertIcon(type);
        
        alertDiv.innerHTML = `
            ${icon}
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        const loginCard = document.querySelector('.login-card');
        loginCard.insertBefore(alertDiv, loginCard.firstChild);

        // Auto-fermeture pour les alertes de succès
        if (type === 'success') {
            setTimeout(() => {
                alertDiv.remove();
            }, 1500);
        }
    }

    // Fonction pour obtenir l'icône appropriée
    function getAlertIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle me-2"></i>',
            danger: '<i class="fas fa-exclamation-circle me-2"></i>',
            info: '<i class="fas fa-info-circle me-2"></i>',
            warning: '<i class="fas fa-exclamation-triangle me-2"></i>'
        };
        return icons[type] || '';
    }

    // Gestion des alertes existantes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.className = 'btn-close';
        closeButton.setAttribute('data-bs-dismiss', 'alert');
        closeButton.setAttribute('aria-label', 'Close');
        alert.appendChild(closeButton);

        // Auto-fermeture pour les alertes de succès
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.remove();
            }, 1500);
        }
    });

    // Gestion de l'affichage/masquage du mot de passe
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
});
