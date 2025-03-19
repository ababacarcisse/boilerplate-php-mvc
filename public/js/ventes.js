document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments DOM
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const ventesTable = document.getElementById('ventesTable');
    const sortableHeaders = document.querySelectorAll('.sortable');
    const tableRows = ventesTable ? ventesTable.querySelectorAll('tbody tr') : [];
    const viewButtons = document.querySelectorAll('.view-sale');
    const editButtons = document.querySelectorAll('.edit-sale');
    const deleteButtons = document.querySelectorAll('.delete-sale');
    const printButtons = document.querySelectorAll('.print-sale');
    
    // Modals
    const saleDetailsModal = document.getElementById('saleDetailsModal');
    const closeSaleDetailsModal = document.getElementById('closeSaleDetailsModal');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const saleDetailsContent = document.getElementById('saleDetailsContent');
    
    const deleteSaleConfirmModal = document.getElementById('deleteSaleConfirmModal');
    const closeDeleteSaleConfirmModal = document.getElementById('closeDeleteSaleConfirmModal');
    const cancelSaleDeleteBtn = document.getElementById('cancelSaleDelete');
    const confirmSaleDeleteBtn = document.getElementById('confirmSaleDelete');
    
    // Variables de gestion de l'état
    let currentDeleteId = null;
    
    // Fonction pour filtrer le tableau
    function filterTable() {
        if (!tableRows) return;
        
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value.toLowerCase() : '';
        const startDate = startDateInput && startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput && endDateInput.value ? new Date(endDateInput.value) : null;
        
        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const dateCell = cells[0].textContent; // Date de vente
            const rowDate = new Date(dateCell);
            const invoiceNumber = cells[1].textContent.toLowerCase(); // N° Facture
            const client = cells[2].textContent.toLowerCase(); // Client
            const products = cells[3].textContent.toLowerCase(); // Produits
            const statusCell = cells[5].querySelector('.badge').textContent.toLowerCase(); // Statut
            
            // Vérifier si la ligne correspond à tous les critères de filtrage
            const matchesSearch = !searchTerm || 
                invoiceNumber.includes(searchTerm) || 
                client.includes(searchTerm) || 
                products.includes(searchTerm);
                
            const matchesStatus = !statusValue || 
                statusCell.includes(statusValue);
                
            const matchesDateRange = (!startDate || rowDate >= startDate) && 
                (!endDate || rowDate <= endDate);
            
            if (matchesSearch && matchesStatus && matchesDateRange) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Initialiser les écouteurs d'événements pour le filtre
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);
    if (startDateInput) startDateInput.addEventListener('change', filterTable);
    if (endDateInput) endDateInput.addEventListener('change', filterTable);
    
    // Fonction pour le tri du tableau
    function sortTable(column, element) {
        if (!ventesTable) return;
        
        const tbody = ventesTable.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let sortDirection = 'asc';
        
        // Déterminer la direction du tri
        if (element.classList.contains('sorted-asc')) {
            sortDirection = 'desc';
            element.classList.remove('sorted-asc');
            element.classList.add('sorted-desc');
        } else if (element.classList.contains('sorted-desc')) {
            sortDirection = 'asc';
            element.classList.remove('sorted-desc');
            element.classList.add('sorted-asc');
        } else {
            // Réinitialiser toutes les colonnes
            sortableHeaders.forEach(header => {
                header.classList.remove('sorted-asc', 'sorted-desc');
            });
            element.classList.add('sorted-asc');
        }
        
        // Tri des lignes
        rows.sort((a, b) => {
            let valueA, valueB;
            
            // Déterminer les valeurs à comparer en fonction de la colonne
            if (column === 'date') {
                valueA = new Date(a.cells[0].textContent);
                valueB = new Date(b.cells[0].textContent);
            } else if (column === 'invoice') {
                valueA = a.cells[1].textContent.toLowerCase();
                valueB = b.cells[1].textContent.toLowerCase();
            } else if (column === 'client') {
                valueA = a.cells[2].textContent.toLowerCase();
                valueB = b.cells[2].textContent.toLowerCase();
            } else if (column === 'products') {
                valueA = a.cells[3].textContent.toLowerCase();
                valueB = b.cells[3].textContent.toLowerCase();
            } else if (column === 'amount') {
                valueA = parseFloat(a.cells[4].textContent.replace(/[^0-9.-]+/g,""));
                valueB = parseFloat(b.cells[4].textContent.replace(/[^0-9.-]+/g,""));
            } else if (column === 'status') {
                valueA = a.cells[5].querySelector('.badge').textContent.toLowerCase();
                valueB = b.cells[5].querySelector('.badge').textContent.toLowerCase();
            } else {
                return 0;
            }
            
            // Comparer les valeurs
            if (valueA < valueB) {
                return sortDirection === 'asc' ? -1 : 1;
            } else if (valueA > valueB) {
                return sortDirection === 'asc' ? 1 : -1;
            } else {
                return 0;
            }
        });
        
        // Réinsérer les lignes triées dans le tableau
        rows.forEach(row => {
            tbody.appendChild(row);
        });
    }
    
    // Ajouter les écouteurs pour le tri
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sort');
            sortTable(column, this);
        });
    });
    
    // Fonction pour afficher les détails d'une vente
    function showSaleDetails(saleId) {
        // Dans une application réelle, vous feriez une requête AJAX pour obtenir les détails
        // Pour cet exemple, nous utilisons des données statiques
        
        // Exemple de données pour la vente avec l'ID 1
        const detailsHTML = `
            <div class="details-section">
                <h5 class="mb-3">Informations générales</h5>
                <div class="row mb-2">
                    <div class="col-4 details-label">Date de vente:</div>
                    <div class="col-8 details-value">2023-07-15</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 details-label">N° Facture:</div>
                    <div class="col-8 details-value">FACT-2023-001</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 details-label">Client:</div>
                    <div class="col-8 details-value">Hôpital Central</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 details-label">Mode de paiement:</div>
                    <div class="col-8 details-value">Virement bancaire</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 details-label">Statut:</div>
                    <div class="col-8 details-value"><span class="badge bg-success">Payée</span></div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 details-label">Remarques:</div>
                    <div class="col-8 details-value">Commande urgente livrée le jour même</div>
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
                            <td>30</td>
                            <td>800 FCFA</td>
                            <td>24,000 FCFA</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold">79,000 FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="details-section">
                <h5 class="mb-3">Historique</h5>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">15 Juillet 2023, 09:30</div>
                            <p class="mb-0">Vente enregistrée</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">15 Juillet 2023, 14:45</div>
                            <p class="mb-0">Produits livrés au client</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-date">18 Juillet 2023, 11:20</div>
                            <p class="mb-0">Paiement reçu</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (saleDetailsContent) {
            saleDetailsContent.innerHTML = detailsHTML;
        }
        
        if (saleDetailsModal) {
            saleDetailsModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Gestion des boutons d'affichage des détails
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const saleId = this.getAttribute('data-id');
            showSaleDetails(saleId);
        });
    });
    
    // Fermer le modal de détails
    function closeDetailsModal() {
        if (saleDetailsModal) {
            saleDetailsModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    if (closeSaleDetailsModal) {
        closeSaleDetailsModal.addEventListener('click', closeDetailsModal);
    }
    
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', closeDetailsModal);
    }
    
    // Gestion des boutons d'édition
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const saleId = this.getAttribute('data-id');
            console.log(`Modifier la vente ID: ${saleId}`);
            
            // Dans une application réelle, vous redirigeriez vers une page d'édition
            // ou ouvririez un modal d'édition avec les données pré-remplies
            
            // Pour cet exemple, ouvrons le modal de vente du dashboard (s'il existe)
            const salesModal = document.getElementById('salesModal');
            if (salesModal) {
                salesModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Changez le titre du modal pour indiquer l'édition
                const modalTitle = salesModal.querySelector('.modal-header h3');
                if (modalTitle) {
                    modalTitle.textContent = 'Modifier une Vente';
                }
            }
        });
    });
    
    // Gestion des boutons d'impression
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const saleId = this.getAttribute('data-id');
            console.log(`Imprimer la facture ID: ${saleId}`);
            
            // Simuler l'impression (dans une application réelle, vous généreriez un PDF)
            alert('Génération de la facture en cours...');
            
            // Exemple de redirection vers une page d'impression
            // window.open(`/factures/print/${saleId}`, '_blank');
        });
    });
    
    // Gestion des boutons de suppression
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentDeleteId = this.getAttribute('data-id');
            
            // Afficher le modal de confirmation
            if (deleteSaleConfirmModal) {
                deleteSaleConfirmModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Fermer le modal de confirmation de suppression
    function closeDeleteModal() {
        if (deleteSaleConfirmModal) {
            deleteSaleConfirmModal.style.display = 'none';
            document.body.style.overflow = '';
            currentDeleteId = null;
        }
    }
    
    if (closeDeleteSaleConfirmModal) {
        closeDeleteSaleConfirmModal.addEventListener('click', closeDeleteModal);
    }
    
    if (cancelSaleDeleteBtn) {
        cancelSaleDeleteBtn.addEventListener('click', closeDeleteModal);
    }
    
    // Confirmer la suppression
    if (confirmSaleDeleteBtn) {
        confirmSaleDeleteBtn.addEventListener('click', function() {
            // Dans une application réelle, vous enverriez une requête au serveur pour supprimer la vente
            
            // Pour la démonstration, supprimons la ligne du tableau
            if (currentDeleteId) {
                const rowToDelete = document.querySelector(`.delete-sale[data-id="${currentDeleteId}"]`).closest('tr');
                if (rowToDelete) {
                    rowToDelete.remove();
                }
            }
            
            // Fermer le modal
            closeDeleteModal();
            
            // Afficher une notification de succès
            alert('Vente supprimée avec succès!');
        });
    }
    
    // Fermer les modals en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        if (e.target === saleDetailsModal) {
            closeDetailsModal();
        }
        if (e.target === deleteSaleConfirmModal) {
            closeDeleteModal();
        }
    });
    
    // Initialiser les compteurs de statistiques avec animation
    function animateValue(obj, start, end, duration) {
        if (!obj) return;
        
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = Math.floor(progress * (end - start) + start);
            obj.textContent = currentValue.toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    // Animations pour les stats
    const totalSalesCounter = document.getElementById('totalSalesCounter');
    const monthlySalesCounter = document.getElementById('monthlySalesCounter');
    const avgSaleAmountCounter = document.getElementById('avgSaleAmountCounter');
    const pendingPaymentsCounter = document.getElementById('pendingPaymentsCounter');
    
    if (totalSalesCounter) animateValue(totalSalesCounter, 0, 1254, 1500);
    if (monthlySalesCounter) animateValue(monthlySalesCounter, 0, 87, 1500);
    if (avgSaleAmountCounter) animateValue(avgSaleAmountCounter, 0, 45000, 1500);
    if (pendingPaymentsCounter) animateValue(pendingPaymentsCounter, 0, 3, 1500);
});

