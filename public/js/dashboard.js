document.addEventListener('DOMContentLoaded', () => {
    // Exemple de mise à jour dynamique des données
    document.getElementById('total-stock').textContent = '127';
    document.getElementById('total-value').textContent = '2456';
    document.getElementById('sales-today').textContent = '89';
    document.getElementById('alerts').textContent = '5';

    // Gestion du popup d'entrée de stock
    const stockEntryModal = document.getElementById('stockEntryModal');
    const openStockEntryModalBtn = document.getElementById('openStockEntryModal');
    const closeStockEntryModalBtn = document.getElementById('closeStockEntryModal');
    const cancelStockEntryBtn = document.getElementById('cancelStockEntry');
    const saveStockEntryBtn = document.getElementById('saveStockEntry');
    const stockEntryForm = document.getElementById('stockEntryForm');
    
    // Champs pour calcul automatique
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unitPrice');
    const totalPriceInput = document.getElementById('totalPrice');
    
    // Définir la date du jour par défaut pour la date d'entrée
    if (document.getElementById('entryDate')) {
        document.getElementById('entryDate').valueAsDate = new Date();
    }
    
    // Ouvrir le popup
    if (openStockEntryModalBtn) {
        openStockEntryModalBtn.addEventListener('click', () => {
            stockEntryModal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Empêcher le défilement en arrière-plan
        });
    }
    
    // Fermer le popup
    const closeEntryModal = () => {
        if (stockEntryModal) {
            stockEntryModal.style.display = 'none';
            document.body.style.overflow = ''; // Réactiver le défilement
            if (stockEntryForm) stockEntryForm.reset(); // Réinitialiser le formulaire
            
            // Réinitialiser le titre du modal si nécessaire
            const modalTitle = stockEntryModal.querySelector('.modal-header h3');
            if (modalTitle) {
                modalTitle.textContent = 'Enregistrer une Nouvelle Entrée de Stock';
            }
        }
    };
    
    if (closeStockEntryModalBtn) {
        // Supprimer les écouteurs existants pour éviter les doublons
        closeStockEntryModalBtn.replaceWith(closeStockEntryModalBtn.cloneNode(true));
        // Réattacher l'écouteur
        document.getElementById('closeStockEntryModal').addEventListener('click', closeEntryModal);
    }
    
    if (cancelStockEntryBtn) {
        // Supprimer les écouteurs existants pour éviter les doublons
        cancelStockEntryBtn.replaceWith(cancelStockEntryBtn.cloneNode(true));
        // Réattacher l'écouteur
        document.getElementById('cancelStockEntry').addEventListener('click', closeEntryModal);
    }
    
    // Cliquer en dehors du popup pour le fermer
    window.addEventListener('click', (e) => {
        if (e.target === stockEntryModal) {
            closeEntryModal();
        }
    });
    
    // Calcul automatique du prix total
    const calculateTotal = () => {
        if (quantityInput && unitPriceInput && totalPriceInput) {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const total = quantity * unitPrice;
            totalPriceInput.value = total.toFixed(2);
        }
    };
    
    if (quantityInput) {
        quantityInput.addEventListener('input', calculateTotal);
    }
    
    if (unitPriceInput) {
        unitPriceInput.addEventListener('input', calculateTotal);
    }
    
    // Validation et soumission du formulaire
    if (saveStockEntryBtn && stockEntryForm) {
        saveStockEntryBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Vérifier si le formulaire est valide
            const isValid = stockEntryForm.checkValidity();
            if (isValid) {
                // Collecter les données du formulaire
                const formData = {
                    entryDate: document.getElementById('entryDate').value,
                    expiryDate: document.getElementById('expiryDate').value,
                    productName: document.getElementById('productName').value,
                    quantity: document.getElementById('quantity').value,
                    unitPrice: document.getElementById('unitPrice').value,
                    totalPrice: document.getElementById('totalPrice').value,
                    supplier: document.getElementById('supplier').value,
                    invoiceNumber: document.getElementById('invoiceNumber').value,
                    deliveryNote: document.getElementById('deliveryNote').value,
                    category: document.getElementById('category').value
                };
                
                // Ici, vous pouvez ajouter le code pour envoyer les données au serveur
                console.log('Données d\'entrée de stock:', formData);
                
                // Fermer le modal après l'envoi
                alert('Entrée de stock enregistrée avec succès!');
                closeEntryModal();
            } else {
                // Forcer l'affichage des messages de validation
                stockEntryForm.reportValidity();
            }
        });
    }

    // ====== GESTION DU POPUP DE SORTIE DE STOCK ======
    const stockOutputModal = document.getElementById('stockOutputModal');
    const openStockOutputModalBtn = document.getElementById('openStockOutputModal');
    const closeStockOutputModalBtn = document.getElementById('closeStockOutputModal');
    const cancelStockOutputBtn = document.getElementById('cancelStockOutput');
    const saveStockOutputBtn = document.getElementById('saveStockOutput');
    const generalOutputForm = document.getElementById('generalOutputForm');
    const internalOutputForm = document.getElementById('internalOutputForm');
    
    // Éléments des onglets
    const generalTab = document.getElementById('general-tab');
    const internalTab = document.getElementById('internal-tab');
    const outputModalTitle = document.getElementById('outputModalTitle');
    
    // Définir les dates par défaut
    if (document.getElementById('generalOutputDate')) {
        document.getElementById('generalOutputDate').valueAsDate = new Date();
    }
    if (document.getElementById('internalOutputDate')) {
        document.getElementById('internalOutputDate').valueAsDate = new Date();
    }
    
    // Mettre à jour le titre en fonction de l'onglet actif
    const updateOutputTitle = (isGeneral) => {
        if (outputModalTitle) {
            outputModalTitle.textContent = isGeneral ? 
                'Enregistrer une Sortie Générale' : 
                'Enregistrer une Sortie Interne';
        }
    };
    
    if (generalTab) {
        generalTab.addEventListener('click', () => updateOutputTitle(true));
    }
    
    if (internalTab) {
        internalTab.addEventListener('click', () => updateOutputTitle(false));
    }
    
    // Ouvrir le popup
    if (openStockOutputModalBtn) {
        openStockOutputModalBtn.addEventListener('click', () => {
            stockOutputModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            updateOutputTitle(true); // Par défaut, le titre est pour la sortie générale
        });
    }
    
    // Fermer le popup
    const closeOutputModal = () => {
        stockOutputModal.style.display = 'none';
        document.body.style.overflow = '';
        if (generalOutputForm) generalOutputForm.reset();
        if (internalOutputForm) internalOutputForm.reset();
    };
    
    if (closeStockOutputModalBtn) {
        closeStockOutputModalBtn.addEventListener('click', closeOutputModal);
    }
    
    if (cancelStockOutputBtn) {
        cancelStockOutputBtn.addEventListener('click', closeOutputModal);
    }
    
    // Cliquer en dehors du popup pour le fermer
    window.addEventListener('click', (e) => {
        if (e.target === stockOutputModal) {
            closeOutputModal();
        }
    });
    
    // Validation et soumission du formulaire de sortie
    if (saveStockOutputBtn) {
        saveStockOutputBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Déterminer quel formulaire est actif
            const isGeneralActive = document.querySelector('#general-tab').classList.contains('active');
            const activeForm = isGeneralActive ? generalOutputForm : internalOutputForm;
            
            // Vérifier si le formulaire est valide
            const isValid = activeForm.checkValidity();
            if (isValid) {
                // Collecter les données du formulaire actif
                let formData;
                
                if (isGeneralActive) {
                    formData = {
                        outputDate: document.getElementById('generalOutputDate').value,
                        designation: document.getElementById('generalDesignation').value,
                        quantity: document.getElementById('generalQuantity').value,
                        destination: document.getElementById('generalDestination').value,
                        outputType: document.getElementById('generalOutputType').value
                    };
                } else {
                    formData = {
                        outputDate: document.getElementById('internalOutputDate').value,
                        requestingService: document.getElementById('requestingService').value,
                        designation: document.getElementById('internalDesignation').value,
                        requestedQuantity: document.getElementById('requestedQuantity').value,
                        deliveredQuantity: document.getElementById('deliveredQuantity').value,
                        totalPrice: document.getElementById('internalTotalPrice').value,
                        expiryDate: document.getElementById('expiryDateOutput').value
                    };
                }
                
                // Ici, vous pouvez ajouter le code pour envoyer les données au serveur
                console.log(`Données de sortie ${isGeneralActive ? 'générale' : 'interne'}:`, formData);
                
                // Fermer le modal après l'envoi
                alert(`Sortie ${isGeneralActive ? 'générale' : 'interne'} enregistrée avec succès!`);
                closeOutputModal();
            } else {
                // Forcer l'affichage des messages de validation
                activeForm.reportValidity();
            }
        });
    }
});