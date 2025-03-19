<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<?php require_once __DIR__ . '/../../../app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorties de Stock - StockSanté</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../../public/css/home.css'; ?>
        <?php include __DIR__ . '/../../../public/css/sorties.css'; ?>
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
                    <h2>Sorties de Stock</h2>
                    <button class="btn btn-primary" id="openStockOutputModal">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter une nouvelle sortie
                    </button>
                </div>
                
                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="typeFilter">
                                    <option value="">Tous les types</option>
                                    <option value="general">Sortie générale</option>
                                    <option value="internal">Sortie interne</option>
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
                
                <!-- Onglets pour les différents types de sorties -->
                <ul class="nav nav-tabs mb-4" id="outputTypesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-outputs-tab" data-bs-toggle="tab" data-bs-target="#all-outputs" type="button" role="tab" aria-controls="all-outputs" aria-selected="true">Toutes les sorties</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-outputs-tab" data-bs-toggle="tab" data-bs-target="#general-outputs" type="button" role="tab" aria-controls="general-outputs" aria-selected="false">Sorties générales</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="internal-outputs-tab" data-bs-toggle="tab" data-bs-target="#internal-outputs" type="button" role="tab" aria-controls="internal-outputs" aria-selected="false">Sorties internes</button>
                    </li>
                </ul>
                
                <!-- Contenu des onglets -->
                <div class="tab-content" id="outputTypesContent">
                    <!-- Toutes les sorties -->
                    <div class="tab-pane fade show active" id="all-outputs" role="tabpanel" aria-labelledby="all-outputs-tab">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="outputsTable">
                                        <thead>
                                            <tr>
                                                <th class="sortable" data-sort="date">Date de sortie <i class="bi bi-arrow-down-up"></i></th>
                                                <th class="sortable" data-sort="type">Type <i class="bi bi-arrow-down-up"></i></th>
                                                <th class="sortable" data-sort="designation">Désignation <i class="bi bi-arrow-down-up"></i></th>
                                                <th class="sortable" data-sort="quantity">Quantité <i class="bi bi-arrow-down-up"></i></th>
                                                <th class="sortable" data-sort="destination">Destination/Service <i class="bi bi-arrow-down-up"></i></th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Exemple de données pour les sorties générales -->
                                            <tr data-type="general">
                                                <td>2023-07-15</td>
                                                <td><span class="badge bg-info">Générale</span></td>
                                                <td>Amoxicilline 500mg</td>
                                                <td>20</td>
                                                <td>Hôpital central</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary view-output" data-id="1" data-type="general"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-outline-danger delete-output" data-id="1" data-type="general"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- Exemple de données pour les sorties internes -->
                                            <tr data-type="internal">
                                                <td>2023-07-18</td>
                                                <td><span class="badge bg-warning">Interne</span></td>
                                                <td>Paracétamol 1000mg</td>
                                                <td>50</td>
                                                <td>Service Urgences</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary view-output" data-id="2" data-type="internal"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-outline-danger delete-output" data-id="2" data-type="internal"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr data-type="general">
                                                <td>2023-07-20</td>
                                                <td><span class="badge bg-info">Générale</span></td>
                                                <td>Ibuprofène 400mg</td>
                                                <td>30</td>
                                                <td>Clinique Saint-Jean</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary view-output" data-id="3" data-type="general"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-outline-danger delete-output" data-id="3" data-type="general"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr data-type="internal">
                                                <td>2023-07-25</td>
                                                <td><span class="badge bg-warning">Interne</span></td>
                                                <td>Métronidazole 500mg</td>
                                                <td>25</td>
                                                <td>Service Pédiatrie</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary view-output" data-id="4" data-type="internal"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-outline-danger delete-output" data-id="4" data-type="internal"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr data-type="internal">
                                                <td>2023-07-28</td>
                                                <td><span class="badge bg-warning">Interne</span></td>
                                                <td>Oméprazole 20mg</td>
                                                <td>15</td>
                                                <td>Service Gériatrie</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary view-output" data-id="5" data-type="internal"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-outline-danger delete-output" data-id="5" data-type="internal"><i class="bi bi-trash"></i></button>
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
                    
                    <!-- Sorties générales -->
                    <div class="tab-pane fade" id="general-outputs" role="tabpanel" aria-labelledby="general-outputs-tab">
                        <!-- Contenu similaire mais filtré pour les sorties générales -->
                    </div>
                    
                    <!-- Sorties internes -->
                    <div class="tab-pane fade" id="internal-outputs" role="tabpanel" aria-labelledby="internal-outputs-tab">
                        <!-- Contenu similaire mais filtré pour les sorties internes -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Modal pour la sortie de stock -->
    <?php include __DIR__ . '/../components/Dashboard/stock_output_popup.php'; ?>
    
    <!-- Modal de détails de sortie -->
    <div class="modal-overlay" id="outputDetailsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h5 id="detailsModalTitle">Détails de la Sortie</h5>
                <button class="close-modal" id="closeOutputDetailsModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body" id="outputDetailsContent">
                <!-- Le contenu sera rempli dynamiquement par JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeDetailsBtn">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal-overlay" id="deleteOutputConfirmModal">
        <div class="modal-container" style="max-width: 400px;">
            <div class="modal-header bg-danger text-white">
                <h5>Confirmer la suppression</h5>
                <button class="close-modal" id="closeDeleteOutputConfirmModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette sortie de stock?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelOutputDelete">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmOutputDelete">Supprimer</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../../public/js/sorties.js'; ?>
    </script>
</body>
</html>