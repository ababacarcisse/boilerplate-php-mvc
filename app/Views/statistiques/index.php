<?php require_once __DIR__ . '/../../../app/config.php'; ?>
<?php require_once __DIR__ . '/../../../app/helpers/AssetHelper.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de Gestion de Stock - StockSanté</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        <?php include __DIR__ . '/../../../public/css/home.css'; ?>
        <?php include __DIR__ . '/../../../public/css/statistiques.css'; ?>
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Main Content -->
    <main class="container-fluid">
        <div class="row">
            <div class="col main-content">
                 <!-- Key Indicators Section -->
                <section class="key-indicators mb-5">
                    <h2>Indicateurs Clés</h2>
                    <div class="indicator-cards">
                        <div class="indicator-card">
                            <div class="indicator-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="indicator-content">
                                <h3>Produits en Stock</h3>
                                <div class="indicator-value" id="totalProductsCounter">0</div>
                                <div class="indicator-trend up">
                                    <i class="bi bi-arrow-up-short"></i>
                                    <span>+5.2% depuis le mois dernier</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="indicator-card">
                            <div class="indicator-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="indicator-content">
                                <h3>Valeur du Stock</h3>
                          <div class="indicator-value" id="stockValueCounter">0</div>
                                <div class="indicator-trend up">
                                    <i class="bi bi-arrow-up-short"></i>
                                    <span>+3.7% depuis le mois dernier</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="indicator-card">
                            <div class="indicator-icon">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="indicator-content">
                                <h3>Ventes Totales</h3>
                                <div class="indicator-value" id="totalSalesCounter">0</div>
                                <div class="indicator-trend down">
                                    <i class="bi bi-arrow-down-short"></i>
                                    <span>-2.1% depuis le mois dernier</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="indicator-card">
                            <div class="indicator-icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="indicator-content">
                                <h3>Produits en Alerte</h3>
                                <div class="indicator-value" id="alertProductsCounter">0</div>
                                <div class="indicator-trend stable">
                                    <i class="bi bi-dash"></i>
                                    <span>Stable depuis le mois dernier</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Charts Section -->
                <section class="charts-section mb-5">
                    <h2>Graphiques d'analyse</h2>
                    <div class="charts-container">
                        <!-- Diagramme 1: Évolution du stock -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Évolution des stocks</h3>
                                <div class="chart-controls">
                                    <select id="stockPeriod" class="form-select form-select-sm">
                                        <option value="monthly" selected>Mensuel</option>
                                        <option value="quarterly">Trimestriel</option>
                                        <option value="yearly">Annuel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="stockChart" width="500" height="300"></canvas>
                            </div>
                        </div>
                        
                        <!-- Diagramme 2: Mouvements des produits -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Mouvements des produits</h3>
                                <div class="chart-controls toggle-buttons">
                                    <button id="showEntries" class="btn-toggle active">Entrées</button>
                                    <button id="showOutputs" class="btn-toggle">Sorties</button>
                                    <button id="showSales" class="btn-toggle">Ventes</button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="movementChart" width="500" height="300"></canvas>
                            </div>
                        </div>
                        
                        <!-- Diagramme 3: Répartition des ventes -->
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Répartition des ventes par catégorie</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="salesDistributionChart" width="500" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Detailed Statistics Section -->
                <section class="detailed-stats mb-5">
                    <div class="section-header">
                        <h2>Statistiques Détaillées</h2>
                        <div class="search-filter">
                            <input type="text" id="statsSearch" class="form-control" placeholder="Rechercher...">
                        </div>
                    </div>
                    
                    <div class="stats-table-container">
                        <table id="statsTable" class="stats-table">
                            <thead>
                                <tr>
                                    <th class="sortable" data-sort="date">Date <i class="bi bi-arrow-down-up"></i></th>
                                    <th class="sortable" data-sort="entries">Entrées <i class="bi bi-arrow-down-up"></i></th>
                                    <th class="sortable" data-sort="outputs">Sorties <i class="bi bi-arrow-down-up"></i></th>
                                    <th class="sortable" data-sort="sales">Ventes <i class="bi bi-arrow-down-up"></i></th>
                                    <th class="sortable" data-sort="stock">Stock Final <i class="bi bi-arrow-down-up"></i></th>
                                    <th class="sortable" data-sort="value">Valeur (FCFA) <i class="bi bi-arrow-down-up"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemple de données -->
                                <tr>
                                    <td>31/07/2023</td>
                                    <td>125</td>
                                    <td>42</td>
                                    <td>38</td>
                                    <td>1450</td>
                                    <td>4,350,000</td>
                                </tr>
                                <tr>
                                    <td>30/07/2023</td>
                                    <td>80</td>
                                    <td>35</td>
                                    <td>45</td>
                                    <td>1405</td>
                                    <td>4,215,000</td>
                                </tr>
                                <tr>
                                    <td>29/07/2023</td>
                                    <td>0</td>
                                    <td>28</td>
                                    <td>32</td>
                                    <td>1360</td>
                                    <td>4,080,000</td>
                                </tr>
                                <tr>
                                    <td>28/07/2023</td>
                                    <td>200</td>
                                    <td>40</td>
                                    <td>30</td>
                                    <td>1420</td>
                                    <td>4,260,000</td>
                                </tr>
                                <tr>
                                    <td>27/07/2023</td>
                                    <td>0</td>
                                    <td>25</td>
                                    <td>35</td>
                                    <td>1290</td>
                                    <td>3,870,000</td>
                                </tr>
                                <tr>
                                    <td>26/07/2023</td>
                                    <td>150</td>
                                    <td>30</td>
                                    <td>25</td>
                                    <td>1350</td>
                                    <td>4,050,000</td>
                                </tr>
                                <tr>
                                    <td>25/07/2023</td>
                                    <td>0</td>
                                    <td>20</td>
                                    <td>30</td>
                                    <td>1255</td>
                                    <td>3,765,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="table-pagination">
                        <button id="prevPage" class="btn btn-pagination"><i class="bi bi-chevron-left"></i> Précédent</button>
                        <div class="pagination-info">Page <span id="currentPage">1</span> sur <span id="totalPages">5</span></div>
                        <button id="nextPage" class="btn btn-pagination">Suivant <i class="bi bi-chevron-right"></i></button>
                    </div>
                </section>
                
                <!-- Expiry Alert Section -->
                <section class="expiry-alert mb-5">
                    <h2>Alertes de Péremption</h2>
                    <div class="alert-cards">
                        <div class="alert-card">
                            <div class="alert-header critical">
                                <div class="alert-title">Péremption Critique</div>
                                <div class="alert-count">8 produits</div>
                            </div>
                            <div class="alert-content">
                                <ul class="alert-list">
                                    <li>
                                        <div class="alert-product">Amoxicilline 500mg</div>
                                        <div class="alert-date">15/08/2023</div>
                                        <div class="alert-quantity">50 unités</div>
                                    </li>
                                    <li>
                                        <div class="alert-product">Paracétamol 1000mg</div>
                                        <div class="alert-date">18/08/2023</div>
                                        <div class="alert-quantity">120 unités</div>
                                    </li>
                                    <li>
                                        <div class="alert-product">Ibuprofène 400mg</div>
                                        <div class="alert-date">20/08/2023</div>
                                        <div class="alert-quantity">85 unités</div>
                                    </li>
                                </ul>
                                <div class="alert-more">
                                    <a href="#" class="alert-link">Voir les 5 autres produits</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert-card">
                            <div class="alert-header warning">
                                <div class="alert-title">Péremption dans les 3 mois</div>
                                <div class="alert-count">15 produits</div>
                            </div>
                            <div class="alert-content">
                                <ul class="alert-list">
                                    <li>
                                        <div class="alert-product">Ciprofloxacine 500mg</div>
                                        <div class="alert-date">15/09/2023</div>
                                        <div class="alert-quantity">75 unités</div>
                                    </li>
                                    <li>
                                        <div class="alert-product">Métronidazole 250mg</div>
                                        <div class="alert-date">30/09/2023</div>
                                        <div class="alert-quantity">100 unités</div>
                                    </li>
                                    <li>
                                        <div class="alert-product">Oméprazole 20mg</div>
                                        <div class="alert-date">15/10/2023</div>
                                        <div class="alert-quantity">60 unités</div>
                                    </li>
                                </ul>
                                <div class="alert-more">
                                    <a href="#" class="alert-link">Voir les 12 autres produits</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Custom JS -->
    <script>
        <?php include __DIR__ . '/../../../public/js/dashboard.js'; ?>
        <?php include __DIR__ . '/../../../public/js/statistiques.js'; ?>
    </script>
</body>
</html>