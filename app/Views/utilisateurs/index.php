<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<?php require_once __DIR__ . '/../../../app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - StockSanté</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../../public/css/home.css'; ?>
        <?php include __DIR__ . '/../../../public/css/utilisateurs.css'; ?>
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Main Content -->
    <main class="container-fluid">
        <div class="row">
            <div class="col main-content">
                <div class="page-header">
                    <h1>Gestion des Utilisateurs</h1>
                    <button id="addUserBtn" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill"></i> Ajouter un utilisateur
                    </button>
                </div>

                <!-- Filtres -->
                <div class="filters-container">
                    <div class="search-container">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchUser" class="search-input" placeholder="Rechercher un utilisateur...">
                    </div>
                    <div class="filter-controls">
                        <div class="filter-group">
                            <label for="roleFilter">Rôle:</label>
                            <select id="roleFilter" class="filter-select">
                                <option value="all">Tous</option>
                                <option value="admin">Administrateur</option>
                                <option value="assistant">Assistant</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="typeFilter">Type:</label>
                            <select id="typeFilter" class="filter-select">
                                <option value="all">Tous</option>
                                <option value="mangazin">Mangazin</option>
                                <option value="pharmacie">Pharmacie</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tableau des utilisateurs -->
                <div class="table-responsive">
                    <table id="usersTable" class="users-table">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="name">Nom <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="firstname">Prénom <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="login">Identifiant <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="role">Rôle <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="type">Type <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="status">Statut <i class="bi bi-arrow-down-up"></i></th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Données d'exemple -->
                            <tr>
                                <td>Diallo</td>
                                <td>Mamadou</td>
                                <td>mamadou.diallo</td>
                                <td>Admin</td>
                                <td>Mangazin</td>
                                <td><span class="status-badge active">Actif</span></td>
                                <td>
                                    <button class="btn-view-permissions" data-id="1">
                                        <i class="bi bi-eye"></i> Voir
                                    </button>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" data-id="1" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-delete" data-id="1" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Ndiaye</td>
                                <td>Fatou</td>
                                <td>fatou.ndiaye</td>
                                <td>Admin</td>
                                <td>Pharmacie</td>
                                <td><span class="status-badge active">Actif</span></td>
                                <td>
                                    <button class="btn-view-permissions" data-id="2">
                                        <i class="bi bi-eye"></i> Voir
                                    </button>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" data-id="2" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-delete" data-id="2" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Sow</td>
                                <td>Aissatou</td>
                                <td>aissatou.sow</td>
                                <td>Assistant</td>
                                <td>Pharmacie</td>
                                <td><span class="status-badge active">Actif</span></td>
                                <td>
                                    <button class="btn-view-permissions" data-id="3">
                                        <i class="bi bi-eye"></i> Voir
                                    </button>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" data-id="3" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-delete" data-id="3" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Diop</td>
                                <td>Ousmane</td>
                                <td>ousmane.diop</td>
                                <td>Assistant</td>
                                <td>Mangazin</td>
                                <td><span class="status-badge inactive">Inactif</span></td>
                                <td>
                                    <button class="btn-view-permissions" data-id="4">
                                        <i class="bi bi-eye"></i> Voir
                                    </button>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" data-id="4" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-delete" data-id="4" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container">
                    <button id="prevPage" class="pagination-btn">
                        <i class="bi bi-chevron-left"></i> Précédent
                    </button>
                    <div class="pagination-info">
                        Page <span id="currentPage">1</span> sur <span id="totalPages">1</span>
                    </div>
                    <button id="nextPage" class="pagination-btn">
                        Suivant <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <!-- Add User Modal -->
    <div class="modal-overlay" id="addUserModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitle">Ajouter un utilisateur</h2>
                <button class="close-modal" id="closeAddUserModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <div class="form-group">
                        <label for="lastName">Nom <span class="required">*</span></label>
                        <input type="text" id="lastName" name="lastName" class="form-control" required>
                        <div class="error-message" id="lastNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="firstName">Prénom <span class="required">*</span></label>
                        <input type="text" id="firstName" name="firstName" class="form-control" required>
                        <div class="error-message" id="firstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="login">Identifiant <span class="required">*</span></label>
                        <input type="text" id="login" name="login" class="form-control" required>
                        <div class="error-message" id="loginError"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe <span class="required">*</span></label>
                        <div class="password-input-container">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <button type="button" id="togglePassword" class="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="error-message" id="passwordError"></div>
                    </div>
                    <div class="form-group">
                        <label for="role">Rôle <span class="required">*</span></label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="">-- Sélectionner un rôle --</option>
                            <option value="admin">Administrateur</option>
                            <option value="assistant">Assistant</option>
                        </select>
                        <div class="error-message" id="roleError"></div>
                    </div>
                    <div class="form-group" id="typeContainer">
                        <label for="type">Type d'administrateur <span class="required">*</span></label>
                        <select id="type" name="type" class="form-control">
                            <option value="">-- Sélectionner un type --</option>
                            <option value="mangazin">Mangazin</option>
                            <option value="pharmacie">Pharmacie</option>
                        </select>
                        <div class="error-message" id="typeError"></div>
                    </div>
                    <div class="form-group" id="permissionsContainer">
                        <label>Permissions <span class="required">*</span></label>
                        <div class="permissions-list">
                            <div class="permission-item">
                                <input type="checkbox" id="perm_dashboard" name="permissions[]" value="dashboard">
                                <label for="perm_dashboard">Tableau de bord</label>
                            </div>
                            <div class="permission-item">
                                <input type="checkbox" id="perm_entries" name="permissions[]" value="entries">
                                <label for="perm_entries">Entrées de stock</label>
                            </div>
                            <div class="permission-item">
                                <input type="checkbox" id="perm_outputs" name="permissions[]" value="outputs">
                                <label for="perm_outputs">Sorties de stock</label>
                            </div>
                            <div class="permission-item">
                                <input type="checkbox" id="perm_sales" name="permissions[]" value="sales">
                                <label for="perm_sales">Ventes</label>
                            </div>
                            <div class="permission-item">
                                <input type="checkbox" id="perm_stats" name="permissions[]" value="stats">
                                <label for="perm_stats">Statistiques</label>
                            </div>
                            <div class="permission-item">
                                <input type="checkbox" id="perm_users" name="permissions[]" value="users">
                                <label for="perm_users">Gestion des utilisateurs</label>
                            </div>
                        </div>
                        <div class="error-message" id="permissionsError"></div>
                    </div>
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <div class="status-toggle">
                            <input type="checkbox" id="status" name="status" class="toggle-input" checked>
                            <label for="status" class="toggle-label">
                                <span class="toggle-text-on">Actif</span>
                                <span class="toggle-text-off">Inactif</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelUserForm">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveUser">Enregistrer</button>
            </div>
        </div>
    </div>

    <!-- View Permissions Modal -->
    <div class="modal-overlay" id="permissionsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Permissions de l'utilisateur</h2>
                <button class="close-modal" id="closePermissionsModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="permissionsContent">
                    <!-- Le contenu sera rempli dynamiquement -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closePermissionsBtn">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2>Confirmer la suppression</h2>
                <button class="close-modal" id="closeDeleteConfirmModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet utilisateur?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDelete">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div class="toast-container">
        <div id="notificationToast" class="toast">
            <div class="toast-content">
                <i class="bi bi-check-circle toast-icon success"></i>
                <div class="toast-message" id="toastMessage">Opération réussie !</div>
            </div>
            <button class="toast-close" id="closeToast">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../../public/js/utilisateurs.js'; ?>
    </script>
</body>
</html>