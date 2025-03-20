<div class="details-section">
    <h5 class="mb-3">Informations générales</h5>
    <div class="row mb-2">
        <div class="col-4 details-label">Date de vente:</div>
        <div class="col-8 details-value"><?= $sale['date'] ?? '2023-07-15' ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">N° Facture:</div>
        <div class="col-8 details-value"><?= $sale['invoice'] ?? 'FACT-2023-001' ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">Mode de paiement:</div>
        <div class="col-8 details-value"><?= $sale['payment_method'] ?? 'Espèces' ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">Statut:</div>
        <div class="col-8 details-value">
            <?php
            $statusClass = 'bg-success';
            $statusText = 'Payée';
            
            if (isset($sale['status'])) {
                if ($sale['status'] === 'pending') {
                    $statusClass = 'bg-warning';
                    $statusText = 'En attente';
                } elseif ($sale['status'] === 'cancelled') {
                    $statusClass = 'bg-danger';
                    $statusText = 'Annulée';
                }
            }
            ?>
            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">Remarques:</div>
        <div class="col-8 details-value"><?= $sale['remarks'] ?? 'Achat de médicaments pour besoin personnel' ?></div>
    </div>
</div>

<div class="details-section">
    <h5 class="mb-3">Informations Étudiant</h5>
    <div class="row mb-2">
        <div class="col-4 details-label">Nom et Prénom:</div>
        <div class="col-8 details-value"><?= $sale['student_name'] ?? 'Moussa Diallo' ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">N° Carte COUD:</div>
        <div class="col-8 details-value"><?= $sale['student_card'] ?? 'COUD-2023-1254' ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">Faculté:</div>
        <div class="col-8 details-value"><?= $sale['student_faculty'] ?? 'Faculté de Médecine' ?></div>
    </div>
    <div class="row mb-2">
        <div class="col-4 details-label">Niveau d'étude:</div>
        <div class="col-8 details-value"><?= $sale['student_level'] ?? 'Licence 3' ?></div>
    </div>
</div>

<div class="details-section">
    <h5 class="mb-3">Produits vendus</h5>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($sale['items']) && is_array($sale['items'])): ?>
                <?php foreach ($sale['items'] as $item): ?>
                    <tr>
                        <td><?= $item['name'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['unit_price'], 0, ',', ' ') ?> FCFA</td>
                        <td><?= number_format($item['total'], 0, ',', ' ') ?> FCFA</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>Amoxicilline 500mg</td>
                    <td>50</td>
                    <td>500 FCFA</td>
                    <td>25,000 FCFA</td>
                </tr>
                <tr>
                    <td>Paracétamol 1000mg</td>
                    <td>100</td>
                    <td>300 FCFA</td>
                    <td>30,000 FCFA</td>
                </tr>
                <tr>
                    <td>Oméprazole 20mg</td>
                    <td>40</td>
                    <td>600 FCFA</td>
                    <td>24,000 FCFA</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th><?= isset($sale['total']) ? number_format($sale['total'], 0, ',', ' ') . ' FCFA' : '79,000 FCFA' ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="details-section">
    <h5 class="mb-3">Historique</h5>
    <div class="timeline">
        <div class="timeline-item">
            <div class="timeline-icon">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="timeline-content">
                <h6>Vente créée</h6>
                <p class="timeline-date">15 juillet 2023, 09:45</p>
                <p>Vente enregistrée par Admin</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-icon">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="timeline-content">
                <h6>Paiement reçu</h6>
                <p class="timeline-date">15 juillet 2023, 14:30</p>
                <p>Paiement par espèces. Reçu: RECU-2023-145</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="timeline-content">
                <h6>Vente complétée</h6>
                <p class="timeline-date">15 juillet 2023, 14:35</p>
                <p>Médicaments remis à l'étudiant</p>
            </div>
        </div>
    </div>
</div> 