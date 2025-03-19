<div class="modal-overlay" id="stockEntryModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Enregistrer une Nouvelle Entrée de Stock</h3>
            <button class="close-modal" id="closeStockEntryModal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="stockEntryForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="entryDate" class="form-label">Date d'entrée *</label>
                        <input type="date" class="form-control" id="entryDate" required>
                    </div>
                    <div class="col-md-6">
                        <label for="expiryDate" class="form-label">Date de péremption *</label>
                        <input type="date" class="form-control" id="expiryDate" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="productName" class="form-label">Désignation *</label>
                    <input type="text" class="form-control" id="productName" placeholder="Nom du produit" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantité *</label>
                        <input type="number" class="form-control" id="quantity" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="unitPrice" class="form-label">Prix unitaire *</label>
                        <input type="number" class="form-control" id="unitPrice" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="totalPrice" class="form-label">Prix total</label>
                        <input type="number" class="form-control" id="totalPrice" readonly>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="supplier" class="form-label">Fournisseur *</label>
                    <input type="text" class="form-control" id="supplier" required>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="invoiceNumber" class="form-label">Numéro de facture *</label>
                        <input type="text" class="form-control" id="invoiceNumber" required>
                    </div>
                    <div class="col-md-6">
                        <label for="deliveryNote" class="form-label">Numéro BL/Facture</label>
                        <input type="text" class="form-control" id="deliveryNote">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="category" class="form-label">Catégorie *</label>
                    <select class="form-select" id="category" required>
                        <option value="" selected disabled>Sélectionner une catégorie</option>
                        <option value="antibiotiques">Antibiotiques</option>
                        <option value="antalgiques">Antalgiques</option>
                        <option value="antiinflammatoires">Anti-inflammatoires</option>
                        <option value="vitamines">Vitamines</option>
                        <option value="autres">Autres</option>
                    </select>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancelStockEntry">Annuler</button>
            <button type="submit" class="btn btn-primary" id="saveStockEntry">Enregistrer</button>
        </div>
    </div>
</div> 