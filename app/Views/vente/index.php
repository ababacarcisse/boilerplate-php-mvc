<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<?php require_once __DIR__ . '/../../../app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point de Vente - StockSanté</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../../public/css/home.css'; ?>
        <?php include __DIR__ . '/../../../public/css/vente.css'; ?>
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
                    <h1>Point de Vente</h1>
                    <div class="actions">
                        <button id="resetSaleBtn" style="color:white; background-color: red; height: 30px;">
                            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
                        </button>
                        <button style="color:white; background-color: #007bff; height: 30px;" onclick="window.location.href='<?= BASE_URL ?>/ventes'" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i> Voir les ventes
                        </button>
                    </div>
                </div>

                <!-- Interface POS principale -->
                <div class="pos-container">
                    <!-- Partie gauche: sélection de produits et client -->
                    <div class="pos-left-panel">
                        <div class="customer-section">
                            <h2>Information Client</h2>
                            <div class="form-group">
                                <label for="studentCard">N° Carte COUD</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-person-vcard"></i>
                                    <input type="text" id="studentCard" class="form-control" placeholder="Entrez le n° de carte COUD">
                                </div>
                            </div>
                            <div id="studentInfo" class="student-info-container" style="display: none;">
                                <div class="student-info-header">
                                    <h3 id="studentName">Nom de l'étudiant</h3>
                                    <span id="studentFaculty" class="badge bg-secondary">Faculté</span>
                                </div>
                                <p id="studentLevel">Niveau d'étude</p>
                            </div>
                        </div>

                        <div class="product-search-section">
                            <h2>Ajouter des Produits</h2>
                            <div class="form-group">
                                <label for="productSearch">Recherche de produit</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="productSearch" class="form-control" placeholder="Commencez à taper pour rechercher...">
                                </div>
                                <div id="productSearchResults" class="search-results"></div>
                            </div>
                        </div>

                        <div class="cart-items">
                            <h2>Panier</h2>
                            <div id="cartItemsList" class="cart-items-list">
                                <!-- Les articles seront ajoutés ici dynamiquement -->
                                <div class="empty-cart-message">
                                    <i class="bi bi-cart"></i>
                                    <p>Le panier est vide</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partie droite: résumé et finalisation de la vente -->
                    <div class="pos-right-panel">
                        <div class="order-summary">
                            <h2>Résumé de la vente</h2>
                            <div class="summary-line">
                                <span>Sous-total</span>
                                <span id="subtotalAmount">0 FCFA</span>
                            </div>
                            <div class="summary-line">
                                <span>Nombre d'articles</span>
                                <span id="itemCount">0</span>
                            </div>
                            <div class="summary-line total">
                                <span>Total</span>
                                <span id="totalAmount">0 FCFA</span>
                            </div>

                            <div class="payment-method">
                                <h3>Mode de paiement</h3>
                                <div class="payment-options">
                                    <div class="payment-option">
                                        <input type="radio" id="paymentCash" name="paymentMethod" value="cash" checked>
                                        <label for="paymentCash">Espèces</label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="paymentCard" name="paymentMethod" value="card">
                                        <label for="paymentCard">Carte</label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="paymentTransfer" name="paymentMethod" value="transfer">
                                        <label for="paymentTransfer">Virement</label>
                                    </div>
                                </div>
                            </div>

                            <div class="remarks-section">
                                <label for="saleRemarks">Remarques</label>
                                <textarea id="saleRemarks" class="form-control" placeholder="Ajoutez des remarques..."></textarea>
                            </div>

                            <div class="checkout-actions">
                                <button id="completeSaleBtn" class="btn btn-primary btn-lg btn-block">
                                    <i class="bi bi-check-circle"></i> Finaliser la vente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de confirmation de vente -->
    <div class="modal-overlay" id="saleConfirmModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Confirmer la vente</h2>
                <button class="close-modal" id="closeSaleConfirmModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirm-details">
                    <div class="confirm-student">
                        <h3>Client</h3>
                        <p id="confirmStudentName">N/A</p>
                        <p id="confirmStudentCard">N/A</p>
                    </div>
                    
                    <h3>Résumé des produits</h3>
                    <div id="confirmProductsList" class="confirm-products">
                        <!-- Liste des produits -->
                    </div>
                    
                    <div class="confirm-total">
                        <div class="summary-line">
                            <span>Total</span>
                            <span id="confirmTotalAmount">0 FCFA</span>
                        </div>
                        <div class="summary-line">
                            <span>Mode de paiement</span>
                            <span id="confirmPaymentMethod">Espèces</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelSaleConfirm">Annuler</button>
                <button type="button" class="btn btn-success" id="processSale">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Modal de succès de vente -->
    <div class="modal-overlay" id="saleSuccessModal">
        <div class="modal-container">
            <div class="modal-header success-header">
                <h2><i class="bi bi-check-circle"></i> Vente réussie</h2>
                <button class="close-modal" id="closeSaleSuccessModal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="success-message">
                    <p>La vente a été enregistrée avec succès.</p>
                    <p>Numéro de facture: <strong id="invoiceNumber">FACT-2023-001</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="newSale">Nouvelle vente</button>
                <button type="button" class="btn btn-primary" id="printInvoice">
                    <i class="bi bi-printer"></i> Imprimer la facture
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../../public/js/vente.js'; ?>
    </script>
</body>
</html>