<?php require_once 'app/config.php'; ?>
<?php require_once 'app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - StockSanté</title>
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
                <h2 class="my-4">Tableau de bord</h2>
                <p class="text-muted mb-4">Aperçu de la gestion des stocks et des ventes</p>
                
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
    
    <!-- Modal pour les ventes -->
    <?php include __DIR__ . '/components/Dashboard/sales_popup.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Intégrez vos scripts JS ici -->
    <script>
        <?php include __DIR__ . '/../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../public/js/vente.js'; ?>
    </script>
</body>
</html>
