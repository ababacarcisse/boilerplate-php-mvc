document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments DOM
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const entriesTable = document.getElementById('entriesTable');
    const sortableHeaders = document.querySelectorAll('.sortable');
    const tableRows = entriesTable.querySelectorAll('tbody tr');
    const deleteButtons = document.querySelectorAll('.delete-entry');
    const editButtons = document.querySelectorAll('.edit-entry');
    
    // Modal de confirmation de suppression
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    const closeDeleteConfirmModal = document.getElementById('closeDeleteConfirmModal');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    let currentDeleteId = null;

    // Fonction pour filtrer le tableau
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value.toLowerCase();
        const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
        
        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const dateCell = cells[0].textContent; // Date d'entrée
            const rowDate = new Date(dateCell);
            const designation = cells[1].textContent.toLowerCase(); // Désignation
            const supplier = cells[5].textContent.toLowerCase(); // Fournisseur
            const category = cells[8].textContent.toLowerCase(); // Catégorie
            
            // Vérifier si la ligne correspond à tous les critères de filtrage
            const matchesSearch = !searchTerm || 
                designation.includes(searchTerm) || 
                supplier.includes(searchTerm);
                
            const matchesCategory = !categoryValue || 
                category.includes(categoryValue);
                
            const matchesDateRange = (!startDate || rowDate >= startDate) && 
                (!endDate || rowDate <= endDate);
            
            if (matchesSearch && matchesCategory && matchesDateRange) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Initialiser les écouteurs d'événements pour le filtre
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (categoryFilter) categoryFilter.addEventListener('change', filterTable);
    if (startDateInput) startDateInput.addEventListener('change', filterTable);
    if (endDateInput) endDateInput.addEventListener('change', filterTable);
    
    // Fonction pour le tri du tableau
    function sortTable(column, element) {
        const tbody = entriesTable.querySelector('tbody');
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

        // Déterminer l'index de la colonne
        let columnIndex;
        switch(column) {
            case 'date': columnIndex = 0; break;
            case 'designation': columnIndex = 1; break;
            case 'quantity': columnIndex = 2; break;
            case 'unitPrice': columnIndex = 3; break;
            case 'totalPrice': columnIndex = 4; break;
            case 'supplier': columnIndex = 5; break;
            case 'invoice': columnIndex = 6; break;
            case 'deliveryNote': columnIndex = 7; break;
            case 'category': columnIndex = 8; break;
            case 'expiry': columnIndex = 9; break;
            default: columnIndex = 0;
        }

        // Trier les lignes
        rows.sort((a, b) => {
            let aValue = a.querySelectorAll('td')[columnIndex].textContent.trim();
            let bValue = b.querySelectorAll('td')[columnIndex].textContent.trim();
            
            // Convertir en date ou en nombre si nécessaire
            if (column === 'date' || column === 'expiry') {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            } else if (column === 'quantity') {
                aValue = parseInt(aValue);
                bValue = parseInt(bValue);
            } else if (column === 'unitPrice' || column === 'totalPrice') {
                aValue = parseFloat(aValue.replace('€', '').replace(',', '.').trim());
                bValue = parseFloat(bValue.replace('€', '').replace(',', '.').trim());
            }
            
            if (sortDirection === 'asc') {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });
        
        // Réorganiser les lignes dans le tableau
        rows.forEach(row => tbody.appendChild(row));
    }
    
    // Initialiser les écouteurs d'événements pour le tri
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortColumn = this.getAttribute('data-sort');
            sortTable(sortColumn, this);
        });
    });
    
    // Gestion des boutons d'édition
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const entryId = this.getAttribute('data-id');
            console.log(`Éditer l'entrée ID: ${entryId}`);
            
            // Ouvrir le modal d'édition - pour l'instant, utiliser le même modal que pour l'ajout
            const stockEntryModal = document.getElementById('stockEntryModal');
            if (stockEntryModal) {
                // Ici, vous pourriez pré-remplir le formulaire avec les données de l'entrée
                stockEntryModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Changez le titre du modal pour indiquer l'édition
                const modalTitle = stockEntryModal.querySelector('.modal-header h3');
                if (modalTitle) {
                    modalTitle.textContent = 'Modifier une Entrée de Stock';
                }
                
                // S'assurer que les événements de fermeture fonctionnent aussi en mode édition
                const closeBtn = stockEntryModal.querySelector('#closeStockEntryModal');
                const cancelBtn = stockEntryModal.querySelector('#cancelStockEntry');
                
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        stockEntryModal.style.display = 'none';
                        document.body.style.overflow = '';
                    });
                }
                
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function() {
                        stockEntryModal.style.display = 'none';
                        document.body.style.overflow = '';
                    });
                }
            }
        });
    });
    
    // Gestion des boutons de suppression
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentDeleteId = this.getAttribute('data-id');
            console.log(`Préparer la suppression de l'entrée ID: ${currentDeleteId}`);
            
            // Afficher le modal de confirmation
            if (deleteConfirmModal) {
                deleteConfirmModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Fermer le modal de confirmation
    function closeDeleteModal() {
        if (deleteConfirmModal) {
            deleteConfirmModal.style.display = 'none';
            document.body.style.overflow = '';
            currentDeleteId = null;
        }
    }
    
    if (closeDeleteConfirmModal) {
        closeDeleteConfirmModal.addEventListener('click', closeDeleteModal);
    }
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    }
    
    // Confirmer la suppression
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            console.log(`Suppression confirmée pour l'entrée ID: ${currentDeleteId}`);
            
            // Ici, vous ajouteriez le code pour supprimer réellement l'entrée de la base de données
            
            // Pour la démonstration, supprimons la ligne du tableau
            if (currentDeleteId) {
                const rowToDelete = document.querySelector(`.delete-entry[data-id="${currentDeleteId}"]`).closest('tr');
                if (rowToDelete) {
                    rowToDelete.remove();
                }
            }
            
            // Fermer le modal
            closeDeleteModal();
            
            // Afficher une notification de succès
            alert('Entrée supprimée avec succès!');
        });
    }
    
    // Fermer le modal en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        if (e.target === deleteConfirmModal) {
            closeDeleteModal();
        }
    });
}); 