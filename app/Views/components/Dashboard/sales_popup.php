<div class="modal-overlay" id="salesModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Enregistrer une Nouvelle Vente</h3>
            <button class="close-modal" id="closeSalesModal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="salesForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="saleDate" class="form-label">Date de vente *</label>
                        <input type="date" class="form-control" id="saleDate" required>
                    </div>
                    <div class="col-md-6">
                        <label for="paymentMethod" class="form-label">Mode de paiement *</label>
                        <select class="form-select" id="paymentMethod" required>
                            <option value="" selected disabled>Sélectionner un mode de paiement</option>
                            <option value="cash">Espèces</option>
                            <option value="card">Carte bancaire</option>
                            <option value="transfer">Virement</option>
                            <option value="check">Chèque</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="productName" class="form-label">Produit *</label>
                    <select class="form-select" id="productName" required>
                        <option value="" selected disabled>Sélectionner un produit</option>
                        <option value="paracetamol">Paracétamol 500mg</option>
                        <option value="ibuprofene">Ibuprofène 400mg</option>
                        <option value="amoxicilline">Amoxicilline 1g</option>
                        <option value="metronidazole">Métronidazole 500mg</option>
                        <option value="omeprazole">Oméprazole 20mg</option>
                        <option value="metformine">Metformine 850mg</option>
                    </select>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantité vendue *</label>
                        <input type="number" class="form-control" id="saleQuantity" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="unitPrice" class="form-label">Prix unitaire *</label>
                        <input type="number" class="form-control" id="saleUnitPrice" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="totalPrice" class="form-label">Prix total</label>
                        <input type="number" class="form-control" id="saleTotalPrice" readonly>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="client" class="form-label">Client *</label>
                    <input type="text" class="form-control" id="client" required>
                </div>
                
                <div class="mb-3">
                    <label for="remarks" class="form-label required">Remarques</label>
                    <textarea class="form-control" id="remarks" rows="3" placeholder="Informations complémentaires (facultatif)"></textarea>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelSale">Annuler</button>
            <button type="button" class="btn btn-primary" id="saveSale">Enregistrer</button>
        </div>
    </div>
</div> 