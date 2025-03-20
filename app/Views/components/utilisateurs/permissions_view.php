<div class="permissions-user-info">
    <p class="permissions-user-name"><?= $user['firstName'] ?? '' ?> <?= $user['lastName'] ?? '' ?></p>
    <p class="permissions-user-role">Rôle: <?= $user['role'] ?? '' ?></p>
    <p class="permissions-user-type">Type: <?= ($user['type'] && $user['type'] !== '-') ? $user['type'] : 'N/A' ?></p>
</div>

<h5>Permissions accordées:</h5>
<ul class="permissions-details-list">
    <li>
        <i class="bi <?= isset($permissions['dashboard']) && $permissions['dashboard'] ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied' ?>"></i>
        Accès au tableau de bord
    </li>
    <li>
        <i class="bi <?= isset($permissions['entries']) && $permissions['entries'] ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied' ?>"></i>
        Gestion des entrées de stock
    </li>
    <li>
        <i class="bi <?= isset($permissions['outputs']) && $permissions['outputs'] ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied' ?>"></i>
        Gestion des sorties de stock
    </li>
    <li>
        <i class="bi <?= isset($permissions['sales']) && $permissions['sales'] ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied' ?>"></i>
        Gestion des ventes
    </li>
    <li>
        <i class="bi <?= isset($permissions['stats']) && $permissions['stats'] ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied' ?>"></i>
        Accès aux statistiques
    </li>
    <li>
        <i class="bi <?= isset($permissions['users']) && $permissions['users'] ? 'bi-check-circle permission-granted' : 'bi-x-circle permission-denied' ?>"></i>
        Gestion des utilisateurs
    </li>
</ul> 