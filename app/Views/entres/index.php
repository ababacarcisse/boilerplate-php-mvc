<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<?php require_once __DIR__ . '/../../../app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrées de Stock - StockSanté</title>
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
    <!-- Correction du chemin vers le header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col main-content">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Entrées de Stock</h2>
                    <button class="btn btn-primary" id="openStockEntryModal">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter une nouvelle entrée
                    </button>
                </div>
                
                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="categoryFilter">
                                    <option value="">Toutes les catégories</option>
                                    <option value="antibiotiques">Antibiotiques</option>
                                    <option value="antalgiques">Antalgiques</option>
                                    <option value="antiinflammatoires">Anti-inflammatoires</option>
                                    <option value="vitamines">Vitamines</option>
                                    <option value="autres">Autres</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Du</span>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text">Au</span>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Entries Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="entriesTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="date">Date d'entrée <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="designation">Désignation <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="quantity">Quantité <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="unitPrice">Prix unitaire <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="totalPrice">Prix total <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="supplier">Fournisseur <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="invoice">N° Facture <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="deliveryNote">N° BL/Facture <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="category">Catégorie <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="expiry">Date de péremption <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Exemple de données -->
                                    <tr>
                                        <td>2023-06-15</td>
                                        <td>Amoxicilline 500mg</td>
                                        <td>100</td>
                                        <td>5.50 FCFA</td>
                                        <td>550.00 FCFA</td>
                                        <td>Pharmex SA</td>
                                        <td>F2023-0654</td>
                                        <td>BL-2023-087</td>
                                        <td>Antibiotiques</td>
                                        <td>2025-06-15</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-entry" data-id="1"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger delete-entry" data-id="1"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-06-20</td>
                                        <td>Paracétamol 1000mg</td>
                                        <td>200</td>
                                        <td>3.25 FCFA</td>
                                        <td>650.00 FCFA</td>
                                        <td>MédiSup Distribution</td>
                                        <td>F2023-0728</td>
                                        <td>BL-2023-102</td>
                                        <td>Antalgiques</td>
                                        <td>2025-12-20</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-entry" data-id="2"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger delete-entry" data-id="2"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-06-28</td>
                                        <td>Ibuprofène 400mg</td>
                                        <td>150</td>
                                        <td>4.75 FCFA</td>
                                        <td>712.50 FCFA</td>
                                        <td>Pharmex SA</td>
                                        <td>F2023-0801</td>
                                        <td>BL-2023-115</td>
                                        <td>Anti-inflammatoires</td>
                                        <td>2026-01-28</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-entry" data-id="3"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger delete-entry" data-id="3"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-07-05</td>
                                        <td>Oméprazole 20mg</td>
                                        <td>80</td>
                                        <td>6.50 FCFA</td>
                                        <td>520.00 FCFA</td>
                                        <td>MédiSup Distribution</td>
                                        <td>F2023-0845</td>
                                        <td>BL-2023-122</td>
                                        <td>Anti-ulcéreux</td>
                                        <td>2026-03-05</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-entry" data-id="4"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger delete-entry" data-id="4"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-07-10</td>
                                        <td>Metformine 850mg</td>
                                        <td>120</td>
                                        <td>4.25 FCFA</td>
                                        <td>510.00 FCFA</td>
                                        <td>SantéPharm</td>
                                        <td>F2023-0872</td>
                                        <td>BL-2023-130</td>
                                        <td>Antidiabétiques</td>
                                        <td>2026-05-10</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-entry" data-id="5"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger delete-entry" data-id="5"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Ajouter plus de lignes d'exemple ici si nécessaire -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Précédent</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Suivant</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Correction du chemin vers le footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Correction du chemin vers le popup -->
    <?php include __DIR__ . '/../components/Dashboard/stock_entry_popup.php'; ?>

    <!-- Modal de confirmation de suppression -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal-container" style="max-width: 400px;">
            <div class="modal-header bg-danger text-white">
                <h5>Confirmer la suppression</h5>
                <button class="close-modal" id="closeDeleteConfirmModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette entrée de stock?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDelete">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../../public/js/entres.js'; ?>
    </script>
</body>
</html>
