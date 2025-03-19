<nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/">
            <i class="bi bi-hospital me-2"></i>StockSanté
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == BASE_URL || $_SERVER['REQUEST_URI'] == BASE_URL.'/') ? 'active' : '' ?>" href="<?= BASE_URL ?>/">Tableau de Bord</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], BASE_URL.'/entres') !== false) ? 'active' : '' ?>" href="<?= BASE_URL ?>/entres">Entrées de Stock</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/sorties">Sorties de Stock</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/ventes">Ventes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/statistiques">Statistiques</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/utilisateurs">Utilisateurs</a>
                </li>
            </ul>
        </div>
    </div>
</nav> 