document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments DOM
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const outputsTable = document.getElementById('outputsTable');
    const sortableHeaders = document.querySelectorAll('.sortable');
    const tableRows = outputsTable ? outputsTable.querySelectorAll('tbody tr') : [];
    const viewButtons = document.querySelectorAll('.view-output');
    const deleteButtons = document.querySelectorAll('.delete-output');
    
    // Modals
    const outputDetailsModal = document.getElementById('outputDetailsModal');
    const closeOutputDetailsModal = document.getElementById('closeOutputDetailsModal');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const outputDetailsContent = document.getElementById('outputDetailsContent');
    
    const deleteOutputConfirmModal = document.getElementById('deleteOutputConfirmModal');
    const closeDeleteOutputConfirmModal = document.getElementById('closeDeleteOutputConfirmModal');
    const cancelOutputDeleteBtn = document.getElementById('cancelOutputDelete');
    const confirmOutputDeleteBtn = document.getElementById('confirmOutputDelete');
    
    // Variables de gestion de l'état
    let currentDeleteId = null;
    let currentDeleteType = null;

    // Onglets
    const outputTypesTabs = document.getElementById('outputTypesTabs');
    const allOutputsTab = document.getElementById('all-outputs-tab');
    const generalOutputsTab = document.getElementById('general-outputs-tab');
    const internalOutputsTab = document.getElementById('internal-outputs-tab');
    
    // Fonction pour filtrer le tableau
    function filterTable() {
        if (!tableRows) return;
        
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const typeValue = typeFilter ? typeFilter.value.toLowerCase() : '';
        const startDate = startDateInput && startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput && endDateInput.value ? new Date(endDateInput.value) : null;
        
        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const dateCell = cells[0].textContent; // Date de sortie
            const rowDate = new Date(dateCell);
            const typeCell = cells[1].textContent.toLowerCase(); // Type
            const designation = cells[2].textContent.toLowerCase(); // Désignation
            const destination = cells[4].textContent.toLowerCase(); // Destination/Service
            const rowType = row.getAttribute('data-type'); // Attribut data-type de la ligne
            
            // Vérifier si la ligne correspond à tous les critères de filtrage
            const matchesSearch = !searchTerm || 
                designation.includes(searchTerm) || 
                destination.includes(searchTerm);
                
            const matchesType = !typeValue || 
                rowType === typeValue;
                
            const matchesDateRange = (!startDate || rowDate >= startDate) && 
                (!endDate || rowDate <= endDate);
            
            if (matchesSearch && matchesType && matchesDateRange) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Initialiser les écouteurs d'événements pour le filtre
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (typeFilter) typeFilter.addEventListener('change', filterTable);
    if (startDateInput) startDateInput.addEventListener('change', filterTable);
    if (endDateInput) endDateInput.addEventListener('change', filterTable);
    
    // Fonction pour le tri du tableau
    function sortTable(column, element) {
        if (!outputsTable) return;
        
        const tbody = outputsTable.querySelector('tbody');
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
            } else if (column === 'type') {
                valueA = a.getAttribute('data-type');
                valueB = b.getAttribute('data-type');
            } else if (column === 'designation') {
                valueA = a.cells[2].textContent.toLowerCase();
                valueB = b.cells[2].textContent.toLowerCase();
            } else if (column === 'quantity') {
                valueA = parseInt(a.cells[3].textContent);
                valueB = parseInt(b.cells[3].textContent);
            } else if (column === 'destination') {
                valueA = a.cells[4].textContent.toLowerCase();
                valueB = b.cells[4].textContent.toLowerCase();
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
    
    // Fonction pour afficher les détails d'une sortie
    function showOutputDetails(id, type) {
        // Dans une application réelle, vous récupéreriez ces données depuis le serveur
        // Pour cet exemple, nous utilisons des données fictives
        let detailsHTML = '';
        
        if (type === 'general') {
            detailsHTML = `
                <div class="details-section">
                    <h6>Informations générales</h6>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Date de sortie:</div>
                        <div class="col-8 details-value">2023-07-15</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Type:</div>
                        <div class="col-8 details-value"><span class="badge bg-info">Générale</span></div>
                    </div>
                </div>
                <div class="details-section">
                    <h6>Produit</h6>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Désignation:</div>
                        <div class="col-8 details-value">Amoxicilline 500mg</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Quantité:</div>
                        <div class="col-8 details-value">20</div>
                    </div>
                </div>
                <div class="details-section">
                    <h6>Destination</h6>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Destination:</div>
                        <div class="col-8 details-value">Hôpital central</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Type de sortie:</div>
                        <div class="col-8 details-value">Définitive</div>
                    </div>
                </div>
            `;
        } else if (type === 'internal') {
            detailsHTML = `
                <div class="details-section">
                    <h6>Informations générales</h6>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Date de sortie:</div>
                        <div class="col-8 details-value">2023-07-18</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Type:</div>
                        <div class="col-8 details-value"><span class="badge bg-warning">Interne</span></div>
                    </div>
                </div>
                <div class="details-section">
                    <h6>Service demandeur</h6>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Service:</div>
                        <div class="col-8 details-value">Service Urgences</div>
                    </div>
                </div>
                <div class="details-section">
                    <h6>Produit</h6>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Désignation:</div>
                        <div class="col-8 details-value">Paracétamol 1000mg</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Quantité demandée:</div>
                        <div class="col-8 details-value">60</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Quantité livrée:</div>
                        <div class="col-8 details-value">50</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Prix total:</div>
                        <div class="col-8 details-value">12500 FCFA</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 details-label">Date de péremption:</div>
                        <div class="col-8 details-value">2024-10-15</div>
                    </div>
                </div>
            `;
        }
        
        if (outputDetailsContent) {
            outputDetailsContent.innerHTML = detailsHTML;
        }
        
        if (outputDetailsModal) {
            outputDetailsModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Mettre à jour le titre du modal
            const detailsModalTitle = document.getElementById('detailsModalTitle');
            if (detailsModalTitle) {
                detailsModalTitle.textContent = `Détails de la Sortie - ${type === 'general' ? 'Générale' : 'Interne'}`;
            }
        }
    }
    
    // Gestion des boutons d'affichage des détails
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const outputId = this.getAttribute('data-id');
            const outputType = this.getAttribute('data-type');
            showOutputDetails(outputId, outputType);
        });
    });
    
    // Fermer le modal de détails
    function closeDetailsModal() {
        if (outputDetailsModal) {
            outputDetailsModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    if (closeOutputDetailsModal) {
        closeOutputDetailsModal.addEventListener('click', closeDetailsModal);
    }
    
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', closeDetailsModal);
    }
    
    // Gestion des boutons de suppression
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentDeleteId = this.getAttribute('data-id');
            currentDeleteType = this.getAttribute('data-type');
            
            // Afficher le modal de confirmation
            if (deleteOutputConfirmModal) {
                deleteOutputConfirmModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Fermer le modal de confirmation de suppression
    function closeDeleteModal() {
        if (deleteOutputConfirmModal) {
            deleteOutputConfirmModal.style.display = 'none';
            document.body.style.overflow = '';
            currentDeleteId = null;
            currentDeleteType = null;
        }
    }
    
    if (closeDeleteOutputConfirmModal) {
        closeDeleteOutputConfirmModal.addEventListener('click', closeDeleteModal);
    }
    
    if (cancelOutputDeleteBtn) {
        cancelOutputDeleteBtn.addEventListener('click', closeDeleteModal);
    }
    
    // Confirmer la suppression
    if (confirmOutputDeleteBtn) {
        confirmOutputDeleteBtn.addEventListener('click', function() {
            // Dans une application réelle, vous enverriez une requête au serveur pour supprimer l'entrée
            
            // Pour la démonstration, supprimons la ligne du tableau
            if (currentDeleteId && currentDeleteType) {
                const rowToDelete = document.querySelector(`.delete-output[data-id="${currentDeleteId}"][data-type="${currentDeleteType}"]`).closest('tr');
                if (rowToDelete) {
                    rowToDelete.remove();
                }
            }
            
            // Fermer le modal
            closeDeleteModal();
            
            // Afficher une notification de succès
            alert('Sortie supprimée avec succès!');
        });
    }
    
    // Fermer les modals en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        if (e.target === outputDetailsModal) {
            closeDetailsModal();
        }
        if (e.target === deleteOutputConfirmModal) {
            closeDeleteModal();
        }
    });
    
    // Gestion des onglets pour filtrer par type
    if (outputTypesTabs) {
        if (generalOutputsTab) {
            generalOutputsTab.addEventListener('click', function() {
                typeFilter.value = 'general';
                filterTable();
            });
        }
        
        if (internalOutputsTab) {
            internalOutputsTab.addEventListener('click', function() {
                typeFilter.value = 'internal';
                filterTable();
            });
        }
        
        if (allOutputsTab) {
            allOutputsTab.addEventListener('click', function() {
                typeFilter.value = '';
                filterTable();
            });
        }
    }
});
