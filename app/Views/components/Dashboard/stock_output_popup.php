<div class="modal-overlay" id="stockOutputModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="outputModalTitle">Enregistrer une Sortie de Stock</h3>
            <button class="close-modal" id="closeStockOutputModal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <!-- Onglets pour les types de sortie -->
            <ul class="nav nav-tabs mb-3" id="outputTypeTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-output" type="button" role="tab" aria-controls="general-output" aria-selected="true">Sortie Générale</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="internal-tab" data-bs-toggle="tab" data-bs-target="#internal-output" type="button" role="tab" aria-controls="internal-output" aria-selected="false">Sortie Interne</button>
                </li>
            </ul>
            
            <div class="tab-content" id="outputTypeTabContent">
                <!-- Formulaire pour sortie générale -->
                <div class="tab-pane fade show active" id="general-output" role="tabpanel" aria-labelledby="general-tab">
                    <form id="generalOutputForm">
                        <div class="mb-3">
                            <label for="generalOutputDate" class="form-label">Date de sortie *</label>
                            <input type="date" class="form-control" id="generalOutputDate" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="generalDesignation" class="form-label">Désignation *</label>
                            <input type="text" class="form-control" id="generalDesignation" placeholder="Nom du produit" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="generalQuantity" class="form-label">Quantité *</label>
                            <input type="number" class="form-control" id="generalQuantity" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="generalDestination" class="form-label">Destination *</label>
                            <input type="text" class="form-control" id="generalDestination" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="generalOutputType" class="form-label">Type de sortie *</label>
                            <select class="form-select" id="generalOutputType" required>
                                <option value="" selected disabled>Sélectionner un type</option>
                                <option value="provisional">Provisoire</option>
                                <option value="definitive">Définitive</option>
                            </select>
                        </div>
                    </form>
                </div>
                
                <!-- Formulaire pour sortie interne -->
                <div class="tab-pane fade" id="internal-output" role="tabpanel" aria-labelledby="internal-tab">
                    <form id="internalOutputForm">
                        <div class="mb-3">
                            <label for="internalOutputDate" class="form-label">Date de sortie *</label>
                            <input type="date" class="form-control" id="internalOutputDate" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="requestingService" class="form-label">Service demandeur *</label>
                            <input type="text" class="form-control" id="requestingService" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="internalDesignation" class="form-label">Désignation *</label>
                            <input type="text" class="form-control" id="internalDesignation" placeholder="Nom du produit" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="requestedQuantity" class="form-label">Quantité demandée *</label>
                                <input type="number" class="form-control" id="requestedQuantity" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="deliveredQuantity" class="form-label">Quantité livrée *</label>
                                <input type="number" class="form-control" id="deliveredQuantity" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="internalTotalPrice" class="form-label">Prix total</label>
                            <input type="number" class="form-control" id="internalTotalPrice" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="expiryDateOutput" class="form-label">Date de péremption *</label>
                            <input type="date" class="form-control" id="expiryDateOutput" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelStockOutput">Annuler</button>
            <button type="button" class="btn btn-primary" id="saveStockOutput">Enregistrer</button>
        </div>
    </div>
</div> 