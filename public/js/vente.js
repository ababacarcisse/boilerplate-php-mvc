document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments du DOM pour le modal de vente
    const salesModal = document.getElementById('salesModal');
    const openSalesModalBtn = document.getElementById('openSalesModal');
    const closeSalesModalBtn = document.getElementById('closeSalesModal');
    const cancelSaleBtn = document.getElementById('cancelSale');
    const saveSaleBtn = document.getElementById('saveSale');
    const salesForm = document.getElementById('salesForm');
    
    // Champs pour le calcul automatique du prix total
    const saleQuantityInput = document.getElementById('saleQuantity');
    const saleUnitPriceInput = document.getElementById('saleUnitPrice');
    const saleTotalPriceInput = document.getElementById('saleTotalPrice');
    
    // Définir la date du jour par défaut pour la date de vente
    if (document.getElementById('saleDate')) {
        document.getElementById('saleDate').valueAsDate = new Date();
    }
    
    // Ouvrir le popup de vente
    if (openSalesModalBtn) {
        openSalesModalBtn.addEventListener('click', () => {
            salesModal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Empêcher le défilement en arrière-plan
        });
    }
    
    // Fermer le popup de vente
    const closeSalesModalFunc = () => {
        salesModal.style.display = 'none';
        document.body.style.overflow = ''; // Réactiver le défilement
        if (salesForm) salesForm.reset(); // Réinitialiser le formulaire
    };
    
    if (closeSalesModalBtn) {
        closeSalesModalBtn.addEventListener('click', closeSalesModalFunc);
    }
    
    if (cancelSaleBtn) {
        cancelSaleBtn.addEventListener('click', closeSalesModalFunc);
    }
    
    // Fermer le popup en cliquant en dehors
    window.addEventListener('click', (e) => {
        if (e.target === salesModal) {
            closeSalesModalFunc();
        }
    });
    
    // Calcul automatique du prix total
    const calculateSaleTotal = () => {
        if (saleQuantityInput && saleUnitPriceInput && saleTotalPriceInput) {
            const quantity = parseFloat(saleQuantityInput.value) || 0;
            const unitPrice = parseFloat(saleUnitPriceInput.value) || 0;
            const total = quantity * unitPrice;
            saleTotalPriceInput.value = total.toFixed(2);
        }
    };
    
    if (saleQuantityInput) {
        saleQuantityInput.addEventListener('input', calculateSaleTotal);
    }
    
    if (saleUnitPriceInput) {
        saleUnitPriceInput.addEventListener('input', calculateSaleTotal);
    }
    
    // Validation et soumission du formulaire de vente
    if (saveSaleBtn && salesForm) {
        saveSaleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Vérifier si le formulaire est valide
            const isValid = salesForm.checkValidity();
            if (isValid) {
                // Collecter les données du formulaire
                const formData = {
                    saleDate: document.getElementById('saleDate').value,
                    productName: document.getElementById('productName').value,
                    quantity: document.getElementById('saleQuantity').value,
                    unitPrice: document.getElementById('saleUnitPrice').value,
                    totalPrice: document.getElementById('saleTotalPrice').value,
                    client: document.getElementById('client').value,
                    paymentMethod: document.getElementById('paymentMethod').value,
                    remarks: document.getElementById('remarks').value
                };
                
                // Ici, vous pouvez ajouter le code pour envoyer les données au serveur
                console.log('Données de vente:', formData);
                
                // Fermer le modal après l'envoi
                alert('Vente enregistrée avec succès!');
                closeSalesModalFunc();
            } else {
                // Forcer l'affichage des messages de validation
                salesForm.reportValidity();
            }
        });
    }
});
