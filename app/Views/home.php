<?php 
require_once 'app/config.php'; 
require_once 'app/helpers/AssetHelper.php';
require_once 'app/Lib/Auth.php';

use App\Lib\Auth;

// Vérifier l'authentification et les permissions
$auth = Auth::getInstance();

if (!$auth->isAuthenticated()) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// Récupérer le contexte (pharmacie ou magasin)
$stockContext = $auth->getStockContext();
$isAdmin = $auth->hasRole('admin');
$canSell = $auth->hasPermission('sales');

error_log('Session : ' . print_r($_SESSION, true));
error_log('Auth user : ' . print_r(Auth::getInstance()->getUser(), true));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - <?= ucfirst($stockContext) ?> StockSanté</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../public/css/home.css'; ?>
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include __DIR__ . '/components/header.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col main-content">
                <div class="d-flex justify-content-between align-items-center my-4">
                    <div>
                        <h2 class="mb-1">Tableau de bord</h2>
                        <p class="text-muted mb-0">
                            <?php if ($stockContext === 'pharmacie'): ?>
                                <i class="bi bi-shop me-2"></i>Gestion de la Pharmacie
                            <?php else: ?>
                                <i class="bi bi-box-seam me-2"></i>Gestion du Magasin
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Dashboard Cards -->
                <?php include __DIR__ . '/components/Dashboard/dashboard_cards.php'; ?>
                
                <!-- Recent Activities -->
                <?php include __DIR__ . '/components/Dashboard/recent_activities.php'; ?>
                
                <!-- Popular Products -->
                <?php include __DIR__ . '/components/Dashboard/popular_products.php'; ?>
            </div>

            <!-- Sidebar -->
            <?php include __DIR__ . '/components/Dashboard/sidebar.php'; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>
    
    <!-- Modal pour l'entrée de stock -->
    <?php include __DIR__ . '/components/Dashboard/stock_entry_popup.php'; ?>
    
    <!-- Modal pour la sortie de stock -->
    <?php include __DIR__ . '/components/Dashboard/stock_output_popup.php'; ?>
    
    <!-- Modal pour les ventes (uniquement pour la pharmacie) -->
    <?php if ($canSell): ?>
        <?php include __DIR__ . '/components/Dashboard/sales_popup.php'; ?>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Intégrez vos scripts JS ici -->
    <script>
        <?php include __DIR__ . '/../../public/js/dashboard.js'; ?>
        <?php if ($canSell): ?>
            <?php include __DIR__ . '/../../public/js/vente.js'; ?>
        <?php endif; ?>
    </script>
</body>
</html>
