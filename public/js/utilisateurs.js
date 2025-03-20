document.addEventListener('DOMContentLoaded', function() {
    // Références DOM pour les modals
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const closeAddUserModal = document.querySelector('#addUserModal .close-modal');
    const cancelUserFormBtn = document.getElementById('cancelUserForm');
    const saveUserBtn = document.getElementById('saveUser');
    const userForm = document.getElementById('userForm');
    
    // Références DOM pour le formulaire
    const roleSelect = document.getElementById('role');
    const typeContainer = document.getElementById('typeContainer');
    const permissionsContainer = document.getElementById('permissionsContainer');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    // Références DOM pour les filtres et recherche
    const searchUserInput = document.getElementById('searchUser');
    const roleFilter = document.getElementById('roleFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    // Références DOM pour le modal de permissions
    const permissionsModal = document.getElementById('permissionsModal');
    const closePermissionsModal = document.getElementById('closePermissionsModal');
    const closePermissionsBtn = document.getElementById('closePermissionsBtn');
    const permissionsContent = document.getElementById('permissionsContent');
    
    // Références DOM pour le modal de confirmation de suppression
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    const closeDeleteConfirmModal = document.getElementById('closeDeleteConfirmModal');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    // Références DOM pour les notifications
    const notificationToast = document.getElementById('notificationToast');
    const toastMessage = document.getElementById('toastMessage');
    const closeToastBtn = document.getElementById('closeToast');
    
    // Références DOM pour le tableau des utilisateurs
    const usersTable = document.getElementById('usersTable');
    const viewPermissionsBtns = document.querySelectorAll('.btn-view-permissions');
    const editBtns = document.querySelectorAll('.btn-edit');
    const deleteBtns = document.querySelectorAll('.btn-delete');
    
    // Variables de gestion
    let currentUserId = null;
    let isEditMode = false;
    
    // Fonction pour ouvrir le modal d'ajout d'utilisateur
    function openAddUserModal() {
        if (addUserModal) {
            // Utiliser la classe visible pour l'affichage
            addUserModal.classList.add('visible');
            addUserModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Réinitialiser le formulaire
            if (userForm) {
                userForm.reset();
                
                // Masquer le conteneur de type initialement
                if (typeContainer) {
                    typeContainer.style.display = 'none';
                }
                
                // Afficher le conteneur de permissions
                if (permissionsContainer) {
                    permissionsContainer.style.display = 'block';
                }
                
                // Réinitialiser les messages d'erreur
                const errorMessages = document.querySelectorAll('.error-message');
                errorMessages.forEach(message => {
                    message.textContent = '';
                });
                
                // Réinitialiser le titre du modal
                const modalTitle = document.getElementById('modalTitle');
                if (modalTitle) {
                    modalTitle.textContent = 'Ajouter un utilisateur';
                }
                
                // Réinitialiser le mode d'édition
                isEditMode = false;
                currentUserId = null;
            }
        } else {
            console.error("Modal d'ajout d'utilisateur non trouvé");
        }
    }
    
    // Fonction pour fermer le modal d'ajout d'utilisateur
    function closeAddUserModalFunc() {
        if (addUserModal) {
            addUserModal.classList.remove('visible');
            addUserModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Fonction pour basculer la visibilité du mot de passe
    function togglePasswordVisibility() {
        if (passwordInput) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordBtn.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                togglePasswordBtn.innerHTML = '<i class="bi bi-eye"></i>';
            }
        }
    }
    
    // Fonction pour gérer le changement de rôle
    function handleRoleChange() {
        if (roleSelect && typeContainer) {
            if (roleSelect.value === 'admin') {
                typeContainer.style.display = 'block';
            } else {
                typeContainer.style.display = 'none';
            }
        }
    }
    
    // Fonction pour valider le formulaire
    function validateUserForm() {
        let isValid = true;
        
        // Nom
        const lastName = document.getElementById('lastName');
        const lastNameError = document.getElementById('lastNameError');
        if (lastName && lastNameError) {
            if (!lastName.value.trim()) {
                lastNameError.textContent = 'Le nom est requis';
                isValid = false;
            } else {
                lastNameError.textContent = '';
            }
        }
        
        // Prénom
        const firstName = document.getElementById('firstName');
        const firstNameError = document.getElementById('firstNameError');
        if (firstName && firstNameError) {
            if (!firstName.value.trim()) {
                firstNameError.textContent = 'Le prénom est requis';
                isValid = false;
            } else {
                firstNameError.textContent = '';
            }
        }
        
        // Identifiant
        const login = document.getElementById('login');
        const loginError = document.getElementById('loginError');
        if (login && loginError) {
            if (!login.value.trim()) {
                loginError.textContent = "L'identifiant est requis";
                isValid = false;
            } else {
                loginError.textContent = '';
            }
        }
        
        // Mot de passe (requis uniquement en mode création)
        if (!isEditMode) {
            const password = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            if (password && passwordError) {
                if (!password.value) {
                    passwordError.textContent = 'Le mot de passe est requis';
                    isValid = false;
                } else if (password.value.length < 6) {
                    passwordError.textContent = 'Le mot de passe doit contenir au moins 6 caractères';
                    isValid = false;
                } else {
                    passwordError.textContent = '';
                }
            }
        }
        
        // Rôle
        const role = document.getElementById('role');
        const roleError = document.getElementById('roleError');
        if (role && roleError) {
            if (!role.value) {
                roleError.textContent = 'Le rôle est requis';
                isValid = false;
            } else {
                roleError.textContent = '';
            }
        }
        
        // Type (requis uniquement si le rôle est admin)
        if (role && role.value === 'admin') {
            const type = document.getElementById('type');
            const typeError = document.getElementById('typeError');
            if (type && typeError) {
                if (!type.value) {
                    typeError.textContent = "Le type d'administrateur est requis";
                    isValid = false;
                } else {
                    typeError.textContent = '';
                }
            }
        }
        
        // Permissions (au moins une doit être sélectionnée)
        const permissionsChecked = document.querySelectorAll('input[name="permissions[]"]:checked');
        const permissionsError = document.getElementById('permissionsError');
        if (permissionsError) {
            if (permissionsChecked.length === 0) {
                permissionsError.textContent = 'Sélectionnez au moins une permission';
                isValid = false;
            } else {
                permissionsError.textContent = '';
            }
        }
        
        return isValid;
    }
    
    // Fonction pour sauvegarder l'utilisateur
    function saveUser() {
        if (!validateUserForm()) return;
        
        // Simuler l'enregistrement de l'utilisateur
        // Dans une application réelle, vous enverriez les données au serveur via AJAX
        
        // Récupération des données du formulaire
        const formData = {
            id: currentUserId || Date.now(), // Simuler un ID unique
            lastName: document.getElementById('lastName').value,
            firstName: document.getElementById('firstName').value,
            login: document.getElementById('login').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value,
            type: document.getElementById('role').value === 'admin' ? document.getElementById('type').value : '',
            status: document.getElementById('status').checked,
            permissions: Array.from(document.querySelectorAll('input[name="permissions[]"]:checked')).map(input => input.value)
        };
        
        // Fermer le modal
        closeAddUserModalFunc();
        
        // Afficher une notification de succès
        showNotification(isEditMode ? 'Utilisateur modifié avec succès!' : 'Utilisateur ajouté avec succès!', 'success');
        
        // Recharger les données du tableau (dans une application réelle, vous récupéreriez les données mises à jour du serveur)
        // Pour l'instant, nous simulons l'ajout d'une nouvelle ligne au tableau pour les démonstrations
        if (!isEditMode) {
            addUserToTable(formData);
        } else {
            updateUserInTable(formData);
        }
    }
    
    // Fonction pour ajouter un utilisateur au tableau
    function addUserToTable(userData) {
        const tbody = usersTable.querySelector('tbody');
        const newRow = document.createElement('tr');
        
        // Statut formaté pour l'affichage
        const statusClass = userData.status ? 'active' : 'inactive';
        const statusText = userData.status ? 'Actif' : 'Inactif';
        
        newRow.innerHTML = `
            <td>${userData.lastName}</td>
            <td>${userData.firstName}</td>
            <td>${userData.login}</td>
            <td>${userData.role === 'admin' ? 'Admin' : 'Assistant'}</td>
            <td>${userData.type || '-'}</td>
            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
            <td>
                <button class="btn-view-permissions" data-id="${userData.id}">
                    <i class="bi bi-eye"></i> Voir
                </button>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-edit" data-id="${userData.id}" title="Modifier">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn-delete" data-id="${userData.id}" title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(newRow);
        
        // Ajouter les écouteurs d'événements aux nouveaux boutons
        const viewPermissionsBtn = newRow.querySelector('.btn-view-permissions');
        const editBtn = newRow.querySelector('.btn-edit');
        const deleteBtn = newRow.querySelector('.btn-delete');
        
        viewPermissionsBtn.addEventListener('click', function() {
            showUserPermissions(userData.id);
        });
        
        editBtn.addEventListener('click', function() {
            editUser(userData.id);
        });
        
        deleteBtn.addEventListener('click', function() {
            showDeleteConfirmation(userData.id);
        });
    }
    
    // Fonction pour mettre à jour un utilisateur dans le tableau
    function updateUserInTable(userData) {
        // Trouver la ligne à mettre à jour
        const row = document.querySelector(`.btn-edit[data-id="${userData.id}"]`).closest('tr');
        if (!row) return;
        
        // Mettre à jour les cellules
        const cells = row.querySelectorAll('td');
        cells[0].textContent = userData.lastName;
        cells[1].textContent = userData.firstName;
        cells[2].textContent = userData.login;
        cells[3].textContent = userData.role === 'admin' ? 'Admin' : 'Assistant';
        cells[4].textContent = userData.type || '-';
        
        // Mettre à jour le statut
        const statusBadge = cells[5].querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge ${userData.status ? 'active' : 'inactive'}`;
            statusBadge.textContent = userData.status ? 'Actif' : 'Inactif';
        }
    }
    
    // Fonction pour éditer un utilisateur
    function editUser(userId) {
        // Dans une application réelle, vous récupéreriez les données de l'utilisateur du serveur
        // Pour la démonstration, nous récupérons les données à partir du tableau
        const row = document.querySelector(`.btn-edit[data-id="${userId}"]`).closest('tr');
        if (!row) return;
        
        const cells = row.querySelectorAll('td');
        const userData = {
            id: userId,
            lastName: cells[0].textContent,
            firstName: cells[1].textContent,
            login: cells[2].textContent,
            role: cells[3].textContent === 'Admin' ? 'admin' : 'assistant',
            type: cells[4].textContent !== '-' ? cells[4].textContent : '',
            status: cells[5].querySelector('.status-badge').classList.contains('active')
        };
        
        // Remplir le formulaire avec les données de l'utilisateur
        document.getElementById('lastName').value = userData.lastName;
        document.getElementById('firstName').value = userData.firstName;
        document.getElementById('login').value = userData.login;
        document.getElementById('role').value = userData.role;
        document.getElementById('status').checked = userData.status;
        
        // Gérer l'affichage du conteneur de type en fonction du rôle
        if (userData.role === 'admin' && typeContainer) {
            typeContainer.style.display = 'block';
            document.getElementById('type').value = userData.type;
        } else if (typeContainer) {
            typeContainer.style.display = 'none';
        }
        
        // Dans une application réelle, vous récupéreriez les permissions de l'utilisateur du serveur
        // Pour la démonstration, nous définissons des permissions par défaut
        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = Math.random() > 0.5; // Simuler des permissions aléatoires
        });
        
        // Mettre à jour le titre du modal
        const modalTitle = document.getElementById('modalTitle');
        if (modalTitle) {
            modalTitle.textContent = 'Modifier un utilisateur';
        }
        
        // Mettre à jour les variables de gestion
        isEditMode = true;
        currentUserId = userId;
        
        // Ouvrir le modal
        openAddUserModal();
    }
    
    // Fonction pour afficher les permissions d'un utilisateur
    function showUserPermissions(userId) {
        debug('Affichage des permissions pour l\'utilisateur ID: ' + userId);
        
        // Dans une application réelle, vous récupéreriez les données de l'utilisateur du serveur
        // Pour la démonstration, nous récupérons les informations basiques à partir du tableau
        const row = document.querySelector(`.btn-view-permissions[data-id="${userId}"]`).closest('tr');
        if (!row) return;
        
        const cells = row.querySelectorAll('td');
        const userData = {
            id: userId,
            lastName: cells[0].textContent,
            firstName: cells[1].textContent,
            login: cells[2].textContent,
            role: cells[3].textContent,
            type: cells[4].textContent
        };
        
        // Simuler des permissions pour la démonstration
        const permissions = {
            dashboard: Math.random() > 0.3,
            entries: Math.random() > 0.3,
            outputs: Math.random() > 0.3,
            sales: Math.random() > 0.3,
            stats: Math.random() > 0.3,
            users: Math.random() > 0.7 // Moins probable pour la gestion des utilisateurs
        };
        
        // Construire le contenu HTML pour les permissions
        let permissionsHTML = `
            <div class="permissions-user-info">
                <p class="permissions-user-name">${userData.firstName} ${userData.lastName}</p>
                <p class="permissions-user-role">Rôle: ${userData.role}</p>
                <p class="permissions-user-type">Type: ${userData.type !== '-' ? userData.type : 'N/A'}</p>
            </div>
            
            <h5>Permissions accordées:</h5>
            <ul class="permissions-details-list">
                <li>
                    <i class="bi ${permissions.dashboard ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied'}"></i>
                    Accès au tableau de bord
                </li>
                <li>
                    <i class="bi ${permissions.entries ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied'}"></i>
                    Gestion des entrées de stock
                </li>
                <li>
                    <i class="bi ${permissions.outputs ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied'}"></i>
                    Gestion des sorties de stock
                </li>
                <li>
                    <i class="bi ${permissions.sales ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied'}"></i>
                    Gestion des ventes
                </li>
                <li>
                    <i class="bi ${permissions.stats ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied'}"></i>
                    Accès aux statistiques
                </li>
                <li>
                    <i class="bi ${permissions.users ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied'}"></i>
                    Gestion des utilisateurs
                </li>
            </ul>
        `;
        
        // Mettre à jour le contenu du modal
        permissionsContent.innerHTML = permissionsHTML;
        
        // Assurer que le modal s'affiche correctement
        permissionsModal.classList.add('visible');
        permissionsModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Fonction pour fermer le modal de permissions
    function closePermissionsModalFunc() {
        permissionsModal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Fonction pour afficher le modal de confirmation de suppression
    function showDeleteConfirmation(userId) {
        currentUserId = userId;
        deleteConfirmModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Fonction pour fermer le modal de confirmation de suppression
    function closeDeleteConfirmModalFunc() {
        deleteConfirmModal.style.display = 'none';
        document.body.style.overflow = '';
        currentUserId = null;
    }
    
    // Fonction pour supprimer un utilisateur
    function deleteUser() {
        if (!currentUserId) return;
        
        // Dans une application réelle, vous enverriez une requête au serveur pour supprimer l'utilisateur
        
        // Supprimer la ligne du tableau
        const row = document.querySelector(`.btn-delete[data-id="${currentUserId}"]`).closest('tr');
        if (row) {
            row.remove();
        }
        
        // Fermer le modal de confirmation
        closeDeleteConfirmModalFunc();
        
        // Afficher une notification de succès
        showNotification('Utilisateur supprimé avec succès!', 'success');
    }
    
    // Fonction pour afficher une notification
    function showNotification(message, type = 'success') {
        if (notificationToast && toastMessage) {
            toastMessage.textContent = message;
            
            // Définir l'icône en fonction du type
            const toastIcon = notificationToast.querySelector('.toast-icon');
            if (toastIcon) {
                toastIcon.className = `toast-icon ${type === 'success' ? 'success' : 'error'}`;
                toastIcon.innerHTML = type === 'success' ? 
                    '<i class="bi bi-check-circle"></i>' : 
                    '<i class="bi bi-x-circle"></i>';
            }
            
            // Afficher la notification
            notificationToast.classList.add('show');
            
            // Masquer après 3 secondes
            setTimeout(() => {
                notificationToast.classList.remove('show');
            }, 3000);
        }
    }
    
    // Fonction pour fermer la notification
    function closeNotification() {
        if (notificationToast) {
            notificationToast.classList.remove('show');
        }
    }
    
    // Fonction pour filtrer le tableau des utilisateurs
    function filterUsersTable() {
        const searchTerm = searchUserInput ? searchUserInput.value.toLowerCase() : '';
        const roleValue = roleFilter ? roleFilter.value.toLowerCase() : 'all';
        const typeValue = typeFilter ? typeFilter.value.toLowerCase() : 'all';
        
        const rows = usersTable ? usersTable.querySelectorAll('tbody tr') : [];
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const lastName = cells[0].textContent.toLowerCase();
            const firstName = cells[1].textContent.toLowerCase();
            const login = cells[2].textContent.toLowerCase();
            const role = cells[3].textContent.toLowerCase();
            const type = cells[4].textContent.toLowerCase();
            
            const matchesSearch = lastName.includes(searchTerm) || 
                                firstName.includes(searchTerm) || 
                                login.includes(searchTerm);
                                
            const matchesRole = roleValue === 'all' || 
                              (roleValue === 'admin' && role === 'admin') || 
                              (roleValue === 'assistant' && role === 'assistant');
                              
            const matchesType = typeValue === 'all' || 
                              (type.includes(typeValue) && type !== '-');
            
            if (matchesSearch && matchesRole && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Fonction pour afficher un message de débogage dans la console
    function debug(message) {
        console.log('[DEBUG] ' + message);
    }
    
    // Ajouter un log pour déboguer
    console.log('DOM chargé, initialisation des événements');
    
    // Ajouter cette fonction pour s'assurer que tous les écouteurs d'événements sont correctement initialisés
    function initEventListeners() {
        debug('Initialisation des écouteurs d\'événements');
        
        if (addUserBtn) {
            debug('Initialisation du bouton d\'ajout d\'utilisateur');
            addUserBtn.addEventListener('click', function(e) {
                console.log("Clic sur le bouton d'ajout détecté");
                e.preventDefault();
                if (addUserModal) {
                    console.log("Affichage du modal");
                    addUserModal.classList.add('visible');
                    addUserModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error("Modal non trouvé dans le DOM");
                }
            });
        }
        
        if (closeAddUserModal) {
            closeAddUserModal.addEventListener('click', function() {
                if (addUserModal) {
                    addUserModal.classList.remove('visible');
                    addUserModal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });
        }
        
        if (cancelUserFormBtn) {
            cancelUserFormBtn.addEventListener('click', closeAddUserModalFunc);
        }
        
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', togglePasswordVisibility);
        }
        
        if (roleSelect) {
            roleSelect.addEventListener('change', handleRoleChange);
        }
        
        if (saveUserBtn) {
            saveUserBtn.addEventListener('click', saveUser);
        }
        
        if (closePermissionsModal) {
            closePermissionsModal.addEventListener('click', closePermissionsModalFunc);
        }
        
        if (closePermissionsBtn) {
            closePermissionsBtn.addEventListener('click', closePermissionsModalFunc);
        }
        
        if (closeDeleteConfirmModal) {
            closeDeleteConfirmModal.addEventListener('click', closeDeleteConfirmModalFunc);
        }
        
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', closeDeleteConfirmModalFunc);
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', deleteUser);
        }
        
        if (closeToastBtn) {
            closeToastBtn.addEventListener('click', closeNotification);
        }
        
        if (searchUserInput) {
            searchUserInput.addEventListener('input', filterUsersTable);
        }
        
        if (roleFilter) {
            roleFilter.addEventListener('change', filterUsersTable);
        }
        
        if (typeFilter) {
            typeFilter.addEventListener('change', filterUsersTable);
        }
        
        // Initialiser les boutons du tableau
        viewPermissionsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                showUserPermissions(userId);
            });
        });
        
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                editUser(userId);
            });
        });
        
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                showDeleteConfirmation(userId);
            });
        });
        
        // Fermer les modals en cliquant à l'extérieur
        window.addEventListener('click', function(e) {
            if (e.target === addUserModal) {
                closeAddUserModalFunc();
            } else if (e.target === permissionsModal) {
                closePermissionsModalFunc();
            } else if (e.target === deleteConfirmModal) {
                closeDeleteConfirmModalFunc();
            }
        });
    }
    
    // Appeler cette fonction à la fin du DOMContentLoaded
    initEventListeners();
});
