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
            const studentCard = cells[2].textContent.toLowerCase(); // N° Carte COUD (au lieu de client)
            const statusCell = cells[4].querySelector('.badge').textContent.toLowerCase(); // Statut (index ajusté)
            
            // Vérifier si la ligne correspond à tous les critères de filtrage
            const matchesSearch = !searchTerm || 
                invoiceNumber.includes(searchTerm) || 
                studentCard.includes(searchTerm);
                
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
        // Simulation de récupération des données du serveur
        const saleData = {
            id: saleId,
            date: '2023-07-15',
            invoice: 'FACT-2023-001',
            // Informations étudiant
            student_name: 'Moussa Diallo',
            student_card: 'COUD-2023-1254',
            student_faculty: 'Faculté de Médecine',
            student_level: 'Licence 3',
            // Autres informations
            payment_method: 'Espèces',
            status: 'paid',
            remarks: 'Achat de médicaments pour besoin personnel',
            items: [
                { name: 'Amoxicilline 500mg', quantity: 50, unit_price: 500, total: 25000 },
                { name: 'Paracétamol 1000mg', quantity: 100, unit_price: 300, total: 30000 },
                { name: 'Oméprazole 20mg', quantity: 40, unit_price: 600, total: 24000 }
            ],
            total: 79000
        };
        
        // Construction du HTML pour les détails
        let detailsHTML = `
        <div class="details-section">
            <h5 class="mb-3">Informations générales</h5>
            <div class="row mb-2">
                <div class="col-4 details-label">Date de vente:</div>
                <div class="col-8 details-value">${saleData.date}</div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">N° Facture:</div>
                <div class="col-8 details-value">${saleData.invoice}</div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">Mode de paiement:</div>
                <div class="col-8 details-value">${saleData.payment_method}</div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">Statut:</div>
                <div class="col-8 details-value">
                    <span class="badge ${saleData.status === 'paid' ? 'bg-success' : saleData.status === 'pending' ? 'bg-warning' : 'bg-danger'}">
                        ${saleData.status === 'paid' ? 'Payée' : saleData.status === 'pending' ? 'En attente' : 'Annulée'}
                    </span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">Remarques:</div>
                <div class="col-8 details-value">${saleData.remarks}</div>
            </div>
        </div>

        <div class="details-section">
            <h5 class="mb-3">Informations Étudiant</h5>
            <div class="row mb-2">
                <div class="col-4 details-label">Nom et Prénom:</div>
                <div class="col-8 details-value">${saleData.student_name}</div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">N° Carte COUD:</div>
                <div class="col-8 details-value">${saleData.student_card}</div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">Faculté:</div>
                <div class="col-8 details-value">${saleData.student_faculty}</div>
            </div>
            <div class="row mb-2">
                <div class="col-4 details-label">Niveau d'étude:</div>
                <div class="col-8 details-value">${saleData.student_level}</div>
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
                <tbody>`;
        
        // Ajouter les lignes de produits
        saleData.items.forEach(item => {
            detailsHTML += `
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>${item.unit_price.toLocaleString()} FCFA</td>
                <td>${item.total.toLocaleString()} FCFA</td>
            </tr>`;
        });
        
        // Fermer le tableau et ajouter le total
        detailsHTML += `
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th>${saleData.total.toLocaleString()} FCFA</th>
                    </tr>
                </tfoot>
            </table>
        </div>`;
        
        // Mise à jour du contenu du modal
        if (saleDetailsContent) {
            saleDetailsContent.innerHTML = detailsHTML;
        }
        
        // Afficher le modal
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

