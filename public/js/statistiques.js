document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments DOM
    const periodSelect = document.getElementById('periodSelect');
    const customDateRange = document.getElementById('customDateRange');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const applyDateFilterBtn = document.getElementById('applyDateFilter');
    const statsSearch = document.getElementById('statsSearch');
    const statsTable = document.getElementById('statsTable');
    const sortableHeaders = document.querySelectorAll('.sortable');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const currentPageSpan = document.getElementById('currentPage');
    const totalPagesSpan = document.getElementById('totalPages');
    
    // Références aux contrôles des graphiques
    const stockChartType = document.getElementById('stockChartType');
    const salesChartView = document.getElementById('salesChartView');
    const showEntriesBtn = document.getElementById('showEntries');
    const showOutputsBtn = document.getElementById('showOutputs');
    const showSalesBtn = document.getElementById('showSales');
    
    // Compteurs d'indicateurs
    const totalProductsCounter = document.getElementById('totalProductsCounter');
    const stockValueCounter = document.getElementById('stockValueCounter');
    const totalSalesCounter = document.getElementById('totalSalesCounter');
    const alertProductsCounter = document.getElementById('alertProductsCounter');
    
    // Variables de pagination
    let currentPage = 1;
    const rowsPerPage = 7;
    let totalPages = 1;
    
    // Variables de tri
    let currentSortColumn = 'date';
    let currentSortDirection = 'desc';
    
    // Initialisation des graphiques
    initCharts();
    
    // Initialisation des compteurs avec animation
    animateValue(totalProductsCounter, 0, 1750, 2000);
    animateValue(stockValueCounter, 0, 5250000, 2000, 'FCFA');
    animateValue(totalSalesCounter, 0, 456, 2000);
    animateValue(alertProductsCounter, 0, 23, 2000);
    
    // Eventlisteners
    // Changement de période
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'flex';
            } else {
                customDateRange.style.display = 'none';
                updateChartsForPeriod(this.value);
            }
        });
    }
    
    // Application du filtre de date personnalisé
    if (applyDateFilterBtn) {
        applyDateFilterBtn.addEventListener('click', function() {
            if (startDateInput.value && endDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                
                if (startDate > endDate) {
                    alert('La date de début doit être antérieure à la date de fin.');
                    return;
                }
                
                updateChartsForCustomDateRange(startDate, endDate);
            } else {
                alert('Veuillez sélectionner une date de début et de fin.');
            }
        });
    }
    
    // Recherche dans le tableau
    if (statsSearch) {
        statsSearch.addEventListener('input', filterTable);
    }
    
    // Tri du tableau par en-têtes cliquables
    if (sortableHeaders) {
        sortableHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const column = this.getAttribute('data-sort');
                
                // Si on clique sur la même colonne, inverser l'ordre
                if (column === currentSortColumn) {
                    currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSortColumn = column;
                    currentSortDirection = 'asc';
                }
                
                // Mettre à jour les icônes de tri
                sortableHeaders.forEach(h => {
                    h.classList.remove('sorted-asc', 'sorted-desc');
                });
                
                this.classList.add('sorted-' + currentSortDirection);
                
                // Trier le tableau
                sortTable(column, currentSortDirection);
            });
        });
    }
    
    // Navigation dans la pagination
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                updateTablePagination();
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                updateTablePagination();
            }
        });
    }
    
    // Eventlisteners pour les contrôles de graphiques
    if (stockChartType) {
        stockChartType.addEventListener('change', function() {
            drawStockChart();
        });
    }
    
    if (showEntriesBtn) {
        showEntriesBtn.addEventListener('click', function() {
            showEntriesBtn.classList.add('active');
            showOutputsBtn.classList.remove('active');
            showSalesBtn.classList.remove('active');
            drawMovementChart('entries');
        });
    }
    
    if (showOutputsBtn) {
        showOutputsBtn.addEventListener('click', function() {
            showEntriesBtn.classList.remove('active');
            showOutputsBtn.classList.add('active');
            showSalesBtn.classList.remove('active');
            drawMovementChart('outputs');
        });
    }
    
    if (showSalesBtn) {
        showSalesBtn.addEventListener('click', function() {
            showEntriesBtn.classList.remove('active');
            showOutputsBtn.classList.remove('active');
            showSalesBtn.classList.add('active');
            drawMovementChart('sales');
        });
    }
    
    // Fonctions
    
    // Fonction pour animer les compteurs
    function animateValue(obj, start, end, duration, suffix = '') {
        if (!obj) return;
        
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = Math.floor(progress * (end - start) + start);
            obj.textContent = suffix ? currentValue.toLocaleString() + ' ' + suffix : currentValue.toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    function filterTable() {
        if (!statsTable) return;
        
        const searchTerm = statsSearch.value.toLowerCase();
        const rows = statsTable.querySelectorAll('tbody tr');
        
        let visibleRows = 0;
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let shouldShow = false;
            
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchTerm)) {
                    shouldShow = true;
                }
            });
            
            if (shouldShow) {
                row.dataset.filtered = 'false';
                visibleRows++;
            } else {
                row.dataset.filtered = 'true';
            }
        });
        
        // Recalculer la pagination
        calculateTotalPages(visibleRows);
        currentPage = 1;
        updateTablePagination();
    }
    
    function sortTable(column, direction) {
        if (!statsTable) return;
        
        const tbody = statsTable.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        const sortedRows = rows.sort((a, b) => {
            const aValue = a.cells[getColumnIndex(column)].textContent.trim();
            const bValue = b.cells[getColumnIndex(column)].textContent.trim();
            
            // Déterminer le type de tri (numérique ou alphabétique)
            if (!isNaN(parseFloat(aValue)) && !isNaN(parseFloat(bValue))) {
                // Tri numérique
                const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
                const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
                return direction === 'asc' ? aNum - bNum : bNum - aNum;
            } else {
                // Tri alphabétique
                return direction === 'asc' ? 
                    aValue.localeCompare(bValue, 'fr', { sensitivity: 'base' }) : 
                    bValue.localeCompare(aValue, 'fr', { sensitivity: 'base' });
            }
        });
        
        // Vider et repeupler le tbody
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        sortedRows.forEach(row => {
            tbody.appendChild(row);
        });
        
        // Mettre à jour la pagination
        updateTablePagination();
    }
    
    function getColumnIndex(columnName) {
        const columns = {
            'date': 0,
            'entries': 1,
            'outputs': 2,
            'sales': 3,
            'stock': 4,
            'value': 5
        };
        
        return columns[columnName] || 0;
    }
    
    function updateTablePagination() {
        if (!statsTable) return;
        
        const rows = statsTable.querySelectorAll('tbody tr:not([data-filtered="true"])');
        
        rows.forEach((row, index) => {
            row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) 
                ? '' : 'none';
        });
        
        if (currentPageSpan) currentPageSpan.textContent = currentPage;
        
        // Désactiver/activer les boutons de navigation selon besoin
        if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage === totalPages;
    }
    
    function calculateTotalPages(visibleRows) {
        totalPages = Math.ceil(visibleRows / rowsPerPage);
        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
    }
    
    // Initialiser les graphiques
    function initCharts() {
        // Dessiner les graphiques initiaux
        drawStockChart();
        drawMovementChart('entries');
        drawSalesDistributionBarChart();
        
        // Ajouter des écouteurs d'événements pour les contrôles des graphiques
        if (stockChartType) {
            stockChartType.addEventListener('change', function() {
                drawStockChart();
            });
        }
        
        if (showEntriesBtn) {
            showEntriesBtn.addEventListener('click', function() {
                showEntriesBtn.classList.add('active');
                showOutputsBtn.classList.remove('active');
                showSalesBtn.classList.remove('active');
                drawMovementChart('entries');
            });
        }
        
        if (showOutputsBtn) {
            showOutputsBtn.addEventListener('click', function() {
                showEntriesBtn.classList.remove('active');
                showOutputsBtn.classList.add('active');
                showSalesBtn.classList.remove('active');
                drawMovementChart('outputs');
            });
        }
        
        if (showSalesBtn) {
            showSalesBtn.addEventListener('click', function() {
                showEntriesBtn.classList.remove('active');
                showOutputsBtn.classList.remove('active');
                showSalesBtn.classList.add('active');
                drawMovementChart('sales');
            });
        }
    }
    
    // Dessiner le graphique des mouvements
    function drawMovementChart(type) {
        const canvas = document.getElementById('movementChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Effacer le canvas
        ctx.clearRect(0, 0, width, height);
        
        // Données simulées
        let data;
        let color;
        let title;
        
        switch(type) {
            case 'entries':
                data = [150, 120, 180, 90, 160, 210, 170, 130, 190, 140, 200, 160];
                color = '#0056b3'; // Bleu
                title = 'Entrées de stock';
                break;
            case 'outputs':
                data = [90, 110, 130, 70, 120, 150, 100, 80, 140, 110, 130, 90];
                color = '#fd7e14'; // Orange
                title = 'Sorties de stock';
                break;
            case 'sales':
                data = [60, 75, 85, 50, 90, 110, 70, 55, 95, 80, 100, 65];
                color = '#198754'; // Vert
                title = 'Ventes';
                break;
            default:
                data = [150, 120, 180, 90, 160, 210, 170, 130, 190, 140, 200, 160];
                color = '#0056b3'; // Bleu
                title = 'Entrées de stock';
        }
        
        // Labels des mois
        const labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        // Marges et dimensions
        const margin = { top: 40, right: 20, bottom: 50, left: 60 };
        const innerWidth = width - margin.left - margin.right;
        const innerHeight = height - margin.top - margin.bottom;
        
        // Échelle pour les X (labels)
        const xScale = innerWidth / data.length;
        
        // Trouver la valeur maximale pour l'échelle Y
        const maxValue = Math.max(...data) * 1.1; // 10% de marge supérieure
        
        // Dessiner le titre
        ctx.font = 'bold 16px Arial';
        ctx.fillStyle = '#343a40';
        ctx.textAlign = 'center';
        ctx.fillText(title, width / 2, 20);
        
        // Dessiner l'axe Y avec graduations
        ctx.beginPath();
        ctx.moveTo(margin.left, margin.top);
        ctx.lineTo(margin.left, height - margin.bottom);
        ctx.strokeStyle = '#dee2e6';
        ctx.stroke();
        
        // Graduations Y
        const numYTicks = 5;
        ctx.font = '12px Arial';
        ctx.fillStyle = '#6c757d';
        ctx.textAlign = 'right';
        
        for (let i = 0; i <= numYTicks; i++) {
            const value = (maxValue / numYTicks) * i;
            const yPos = height - margin.bottom - (i * (innerHeight / numYTicks));
            
            ctx.beginPath();
            ctx.moveTo(margin.left - 5, yPos);
            ctx.lineTo(width - margin.right, yPos);
            ctx.strokeStyle = i === 0 ? '#dee2e6' : '#f8f9fa';
            ctx.stroke();
            
            ctx.fillText(Math.round(value).toLocaleString(), margin.left - 10, yPos + 4);
        }
        
        // Dessiner l'axe X avec labels
        ctx.beginPath();
        ctx.moveTo(margin.left, height - margin.bottom);
        ctx.lineTo(width - margin.right, height - margin.bottom);
        ctx.strokeStyle = '#dee2e6';
        ctx.stroke();
        
        // Labels X
        ctx.font = '12px Arial';
        ctx.fillStyle = '#6c757d';
        ctx.textAlign = 'center';
        
        for (let i = 0; i < labels.length; i++) {
            const xPos = margin.left + (i * xScale) + (xScale / 2);
            ctx.fillText(labels[i], xPos, height - margin.bottom + 20);
        }
        
        // Dessiner les barres
        const barWidth = xScale * 0.7;
        
        for (let i = 0; i < data.length; i++) {
            const xPos = margin.left + (i * xScale) + (xScale - barWidth) / 2;
            const barHeight = (data[i] / maxValue) * innerHeight;
            const yPos = height - margin.bottom - barHeight;
            
            // Dégradé pour les barres
            const gradient = ctx.createLinearGradient(xPos, yPos, xPos, height - margin.bottom);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, color + '80'); // Ajouter transparence
            
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.rect(xPos, yPos, barWidth, barHeight);
            ctx.fill();
            
            // Ajouter la valeur au-dessus de la barre
            ctx.font = '10px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            ctx.fillText(data[i], xPos + barWidth / 2, yPos - 5);
            
            // Ajouter un effet de survol
            canvas.addEventListener('mousemove', function(e) {
                const rect = canvas.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const mouseY = e.clientY - rect.top;
                
                // Vérifier si la souris est sur une barre
                if (mouseX >= xPos && mouseX <= xPos + barWidth && 
                    mouseY >= yPos && mouseY <= height - margin.bottom) {
                    // Mettre en surbrillance la barre
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.rect(xPos, yPos, barWidth, barHeight);
                    ctx.fill();
                    
                    // Afficher les détails
                    ctx.fillStyle = '#343a40';
                    ctx.font = 'bold 12px Arial';
                    ctx.fillText(`${labels[i]}: ${data[i]}`, mouseX, mouseY - 10);
                }
            });
        }
    }
    
    // Dessiner le graphique des stocks
    function drawStockChart() {
        const canvas = document.getElementById('stockChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Effacer le canvas
        ctx.clearRect(0, 0, width, height);
        
        // Données simulées pour les produits en stock
        const data = [
            { name: 'Antibiotiques', value: 450 },
            { name: 'Antalgiques', value: 380 },
            { name: 'Anti-inflammatoires', value: 320 },
            { name: 'Vitamines', value: 250 },
            { name: 'Antiseptiques', value: 180 },
            { name: 'Autres', value: 170 }
        ];
        
        // Couleurs pour les catégories
        const colors = [
            '#0056b3', // Bleu
            '#fd7e14', // Orange
            '#198754', // Vert
            '#dc3545', // Rouge
            '#6610f2', // Violet
            '#6c757d'  // Gris
        ];
        
        // Marges et dimensions
        const margin = { top: 40, right: 20, bottom: 70, left: 60 };
        const innerWidth = width - margin.left - margin.right;
        const innerHeight = height - margin.top - margin.bottom;
        
        // Échelle pour les X (catégories)
        const xScale = innerWidth / data.length;
        
        // Trouver la valeur maximale pour l'échelle Y
        const maxValue = Math.max(...data.map(d => d.value)) * 1.1; // 10% de marge supérieure
        
        // Dessiner le titre
        ctx.font = 'bold 16px Arial';
        ctx.fillStyle = '#343a40';
        ctx.textAlign = 'center';
        ctx.fillText('Répartition du stock par catégorie', width / 2, 20);
        
        // Dessiner l'axe Y avec graduations
        ctx.beginPath();
        ctx.moveTo(margin.left, margin.top);
        ctx.lineTo(margin.left, height - margin.bottom);
        ctx.strokeStyle = '#dee2e6';
        ctx.stroke();
        
        // Graduations Y
        const numYTicks = 5;
        ctx.font = '12px Arial';
        ctx.fillStyle = '#6c757d';
        ctx.textAlign = 'right';
        
        for (let i = 0; i <= numYTicks; i++) {
            const value = (maxValue / numYTicks) * i;
            const yPos = height - margin.bottom - (i * (innerHeight / numYTicks));
            
            ctx.beginPath();
            ctx.moveTo(margin.left - 5, yPos);
            ctx.lineTo(width - margin.right, yPos);
            ctx.strokeStyle = i === 0 ? '#dee2e6' : '#f8f9fa';
            ctx.stroke();
            
            ctx.fillText(Math.round(value).toLocaleString(), margin.left - 10, yPos + 4);
        }
        
        // Dessiner l'axe X
        ctx.beginPath();
        ctx.moveTo(margin.left, height - margin.bottom);
        ctx.lineTo(width - margin.right, height - margin.bottom);
        ctx.strokeStyle = '#dee2e6';
        ctx.stroke();
        
        // Labels X
        ctx.font = '12px Arial';
        ctx.fillStyle = '#6c757d';
        ctx.textAlign = 'center';
        
        // Dessiner les barres
        const barWidth = xScale * 0.7;
        
        for (let i = 0; i < data.length; i++) {
            const xPos = margin.left + (i * xScale) + (xScale - barWidth) / 2;
            const barHeight = (data[i].value / maxValue) * innerHeight;
            const yPos = height - margin.bottom - barHeight;
            
            // Dégradé pour les barres
            const gradient = ctx.createLinearGradient(xPos, yPos, xPos, height - margin.bottom);
            gradient.addColorStop(0, colors[i]);
            gradient.addColorStop(1, colors[i] + '80'); // Ajouter transparence
            
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.rect(xPos, yPos, barWidth, barHeight);
            ctx.fill();
            
            // Ajouter la valeur au-dessus de la barre
            ctx.font = '12px Arial';
            ctx.fillStyle = '#343a40';
            ctx.textAlign = 'center';
            ctx.fillText(data[i].value, xPos + barWidth / 2, yPos - 10);
            
            // Ajouter le nom de la catégorie
            ctx.font = '12px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'center';
            
            // Texte en diagonale pour éviter le chevauchement
            ctx.save();
            ctx.translate(xPos + barWidth / 2, height - margin.bottom + 10);
            ctx.rotate(Math.PI / 6); // Rotation de 30 degrés
            ctx.fillText(data[i].name, 0, 0);
            ctx.restore();
        }
    }
    
    // Dessiner le graphique de répartition des ventes
    function drawSalesDistributionBarChart() {
        const canvas = document.getElementById('salesDistributionChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        // Effacer le canvas
        ctx.clearRect(0, 0, width, height);
        
        // Données simulées pour la répartition des ventes
        const data = [
            { name: 'Étudiants', value: 65 },
            { name: 'Personnel', value: 25 },
            { name: 'Externes', value: 10 }
        ];
        
        // Couleurs
        const colors = ['#0056b3', '#fd7e14', '#198754'];
        
        // Dessiner le titre
        ctx.font = 'bold 16px Arial';
        ctx.fillStyle = '#343a40';
        ctx.textAlign = 'center';
        ctx.fillText('Répartition des ventes par type de client', width / 2, 30);
        
        // Paramètres du diagramme en barres
        const chartTopPadding = 50;
        const chartBottomPadding = 60;
        const chartLeftPadding = 50;
        const chartRightPadding = 20;
        const chartWidth = width - chartLeftPadding - chartRightPadding;
        const chartHeight = height - chartTopPadding - chartBottomPadding;
        
        // Calculer la largeur des barres
        const barCount = data.length;
        const barWidth = chartWidth / barCount * 0.6;
        const barSpacing = chartWidth / barCount * 0.4;
        
        // Maximum pour l'échelle
        const maxValue = Math.max(...data.map(item => item.value));
        const scaleFactor = chartHeight / maxValue;
        
        // Grille horizontale et axe Y
        ctx.beginPath();
        ctx.strokeStyle = '#e9ecef';
        ctx.lineWidth = 1;
        
        // Axe Y
        ctx.moveTo(chartLeftPadding, chartTopPadding);
        ctx.lineTo(chartLeftPadding, height - chartBottomPadding);
        ctx.stroke();
        
        // Axe X
        ctx.moveTo(chartLeftPadding, height - chartBottomPadding);
        ctx.lineTo(width - chartRightPadding, height - chartBottomPadding);
        ctx.stroke();
        
        // Intervalles sur l'axe Y
        const yIntervals = 5;
        for (let i = 0; i <= yIntervals; i++) {
            const y = chartTopPadding + chartHeight - (i / yIntervals) * chartHeight;
            const value = (i / yIntervals) * maxValue;
            
            // Ligne de grille
            ctx.beginPath();
            ctx.moveTo(chartLeftPadding, y);
            ctx.lineTo(width - chartRightPadding, y);
            ctx.strokeStyle = '#e9ecef';
            ctx.stroke();
            
            // Valeur sur l'axe Y
            ctx.font = '12px Arial';
            ctx.fillStyle = '#6c757d';
            ctx.textAlign = 'right';
            ctx.fillText(Math.round(value) + '%', chartLeftPadding - 10, y + 4);
        }
        
        // Dessiner les barres avec animation
        for (let i = 0; i < data.length; i++) {
            const barHeight = data[i].value * scaleFactor;
            const x = chartLeftPadding + (i * (barWidth + barSpacing)) + barSpacing;
            const y = height - chartBottomPadding - barHeight;
            
            // Créer un dégradé pour la barre
            const gradient = ctx.createLinearGradient(x, y, x, height - chartBottomPadding);
            gradient.addColorStop(0, colors[i]);
            gradient.addColorStop(1, colors[i] + '80');
            
            // Dessiner la barre
            ctx.fillStyle = gradient;
            ctx.fillRect(x, y, barWidth, barHeight);
            
            // Bordure de la barre
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.strokeRect(x, y, barWidth, barHeight);
            
            // Valeur au-dessus de la barre
            ctx.font = 'bold 14px Arial';
            ctx.fillStyle = colors[i];
            ctx.textAlign = 'center';
            ctx.fillText(`${data[i].value}%`, x + barWidth / 2, y - 10);
            
            // Étiquette de catégorie sous la barre
            ctx.font = '12px Arial';
            ctx.fillStyle = '#343a40';
            ctx.textAlign = 'center';
            ctx.fillText(data[i].name, x + barWidth / 2, height - chartBottomPadding + 20);
        }
    }
    
    // Mettre à jour les graphiques en fonction de la période
    function updateChartsForPeriod(days) {
        console.log(`Mise à jour des graphiques pour les ${days} derniers jours`);
        drawStockChart();
        drawMovementChart('entries');
        drawSalesDistributionBarChart();
    }
    
    // Mettre à jour les graphiques pour une période personnalisée
    function updateChartsForCustomDateRange(startDate, endDate) {
        console.log(`Mise à jour des graphiques du ${startDate.toLocaleDateString()} au ${endDate.toLocaleDateString()}`);
        drawStockChart();
        drawMovementChart('entries');
        drawSalesDistributionBarChart();
    }
});
