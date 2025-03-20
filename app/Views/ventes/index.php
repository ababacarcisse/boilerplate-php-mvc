<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<?php require_once __DIR__ . '/../../../app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Ventes - StockSanté</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../../public/css/home.css'; ?>
        <?php include __DIR__ . '/../../../public/css/ventes.css'; ?>
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col main-content">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Gestion des Ventes</h2>
                    <button class="btn btn-primary"  onclick="window.location.href='<?= BASE_URL ?>/vente'">
                        <i class="bi bi-plus-circle me-2"></i>Enregistrer une nouvelle vente
                    </button>
                </div>
                
                <!-- Statistiques des ventes -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Total des ventes</h6>
                                        <h3 class="card-title stats-value" id="totalSalesCounter">0</h3>
                                    </div>
                                    <div class="stats-icon bg-primary text-white">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Ventes ce mois</h6>
                                        <h3 class="card-title stats-value" id="monthlySalesCounter">0</h3>
                                    </div>
                                    <div class="stats-icon bg-success text-white">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Montant moyen</h6>
                                        <h3 class="card-title stats-value" id="avgSaleAmountCounter">0</h3>
                                        <span class="text-muted">FCFA</span>
                                    </div>
                                    <div class="stats-icon bg-info text-white">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Paiements en attente</h6>
                                        <h3 class="card-title stats-value" id="pendingPaymentsCounter">0</h3>
                                    </div>
                                    <div class="stats-icon bg-warning text-white">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher (client, facture, produit...)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="payée">Payée</option>
                                    <option value="en attente">En attente</option>
                                    <option value="annulée">Annulée</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Du</span>
                                    <input type="date" class="form-control" id="startDate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Au</span>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ventes Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="ventesTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="date">Date <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="invoice">N° Facture <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="client">N° Carte COUD <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="amount">Montant <i class="bi bi-arrow-down-up"></i></th>
                                        <th class="sortable" data-sort="status">Statut <i class="bi bi-arrow-down-up"></i></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Exemple de données -->
                                    <tr>
                                        <td>2023-07-15</td>
                                        <td>FACT-2023-001</td>
                                        <td>COUD-2023-1254</td>
                                        <td>79,000 FCFA</td>
                                        <td><span class="badge bg-success">Payée</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary view-sale" data-id="1" title="Voir les détails"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-outline-secondary edit-sale" data-id="1" title="Modifier"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-info print-sale" data-id="1" title="Imprimer la facture"><i class="bi bi-printer"></i></button>
                                                <button class="btn btn-outline-danger delete-sale" data-id="1" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-07-20</td>
                                        <td>FACT-2023-002</td>
                                        <td>COUD-2023-1876</td>
                                        <td>45,500 FCFA</td>
                                        <td><span class="badge bg-warning">En attente</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary view-sale" data-id="2" title="Voir les détails"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-outline-secondary edit-sale" data-id="2" title="Modifier"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-info print-sale" data-id="2" title="Imprimer la facture"><i class="bi bi-printer"></i></button>
                                                <button class="btn btn-outline-danger delete-sale" data-id="2" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-07-25</td>
                                        <td>FACT-2023-003</td>
                                        <td>COUD-2023-2145</td>
                                        <td>120,800 FCFA</td>
                                        <td><span class="badge bg-success">Payée</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary view-sale" data-id="3" title="Voir les détails"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-outline-secondary edit-sale" data-id="3" title="Modifier"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-info print-sale" data-id="3" title="Imprimer la facture"><i class="bi bi-printer"></i></button>
                                                <button class="btn btn-outline-danger delete-sale" data-id="3" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2023-07-28</td>
                                        <td>FACT-2023-004</td>
                                        <td>COUD-2023-1932</td>
                                        <td>28,500 FCFA</td>
                                        <td><span class="badge bg-danger">Annulée</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary view-sale" data-id="4" title="Voir les détails"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-outline-secondary edit-sale" data-id="4" title="Modifier"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-info print-sale" data-id="4" title="Imprimer la facture"><i class="bi bi-printer"></i></button>
                                                <button class="btn btn-outline-danger delete-sale" data-id="4" title="Supprimer"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
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

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Modal de vente -->
    <?php include __DIR__ . '/../components/Dashboard/sales_popup.php'; ?>
    
    <!-- Modal de détails de vente -->
    <div class="modal-overlay" id="saleDetailsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h5 id="detailsModalTitle">Détails de la Vente</h5>
                <button class="close-modal" id="closeSaleDetailsModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <!-- Le contenu sera rempli dynamiquement par JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeDetailsBtn">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal-overlay" id="deleteSaleConfirmModal">
        <div class="modal-container" style="max-width: 400px;">
            <div class="modal-header bg-danger text-white">
                <h5>Confirmer la suppression</h5>
                <button class="close-modal" id="closeDeleteSaleConfirmModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette vente?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelSaleDelete">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmSaleDelete">Supprimer</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../../public/js/ventes.js'; ?>
    </script>
</body>
</html>