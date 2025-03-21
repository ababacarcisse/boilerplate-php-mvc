<?php
require_once __DIR__ . '/../../../app/config.php';
require_once __DIR__ . '/../../../app/Lib/Auth.php';

use App\Lib\Auth;

// Vérifier l'authentification et les permissions
$auth = Auth::getInstance();

if (!$auth->isAuthenticated()) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// Vérifier la permission d'accès aux entrées
if (!$auth->hasPermission('entries')) {
    header('Location: ' . BASE_URL . '/unauthorized');
    exit;
}

// Récupérer le contexte (pharmacie ou magasin)
$stockContext = $auth->getStockContext();
$isAdmin = $auth->hasRole('admin');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Entrées - <?= ucfirst($stockContext) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../../public/css/home.css'; ?>
        <?php include __DIR__ . '/../../../public/css/entres.css'; ?>
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
                    <h1>Gestion des Entrées - <?= ucfirst($stockContext) ?></h1>
                    <?php if ($isAdmin): ?>
                    <button id="addEntryBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle-fill"></i> Nouvelle entrée
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Filtres -->
                <div class="filters-container">
                    <div class="search-container">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchEntry" class="search-input" placeholder="Rechercher une entrée...">
                    </div>
                    <div class="filter-controls">
                        <div class="filter-group">
                            <label for="dateFilter">Date:</label>
                            <input type="date" id="dateFilter" class="filter-input">
                        </div>
                        <div class="filter-group">
                            <label for="statusFilter">Statut:</label>
                            <select id="statusFilter" class="filter-select">
                                <option value="all">Tous</option>
                                <option value="pending">En attente</option>
                                <option value="completed">Complété</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tableau des entrées -->
                <div class="table-responsive">
                    <table id="entriesTable" class="entries-table">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="date">Date <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="reference">Référence <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="product">Produit <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="quantity">Quantité <i class="bi bi-arrow-down-up"></i></th>
                                <th class="sortable" data-sort="status">Statut <i class="bi bi-arrow-down-up"></i></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les données seront chargées dynamiquement -->
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

    <!-- Modal d'ajout/modification d'entrée -->
    <?php if ($isAdmin): ?>
    <div class="modal-overlay" id="entryModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitle">Nouvelle entrée</h2>
                <button class="close-modal" id="closeEntryModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="entryForm">
                    <div class="form-group">
                        <label for="reference">Référence <span class="required">*</span></label>
                        <input type="text" id="reference" name="reference" class="form-control" required>
                        <div class="error-message" id="referenceError"></div>
                    </div>
                    <div class="form-group">
                        <label for="product">Produit <span class="required">*</span></label>
                        <select id="product" name="product" class="form-control" required>
                            <option value="">-- Sélectionner un produit --</option>
                            <!-- Les produits seront chargés dynamiquement -->
                        </select>
                        <div class="error-message" id="productError"></div>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantité <span class="required">*</span></label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                        <div class="error-message" id="quantityError"></div>
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span class="required">*</span></label>
                        <input type="date" id="date" name="date" class="form-control" required>
                        <div class="error-message" id="dateError"></div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelEntryForm">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveEntry">Enregistrer</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/entres.js'; ?>
    </script>
</body>
</html>
