// Fonction pour formater la date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Traduction des statuts
const statusLabels = {
    '1': 'En Attente',
    '2': 'En Cours',
    '3': 'Terminé'
};

// Traduction des technologies
const technologyLabels = {
    'fo': 'FO',
    'fh': 'FH',
    'blr': 'BLR',
    
};

// Événements
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du menu profil
    const profileMenu = document.getElementById('profile-menu');
    if (profileMenu) {
        profileMenu.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    }

    // Gestion des filtres
    const technologyFilter = document.getElementById('technology-filter');
    const statusFilter = document.getElementById('status-filter');
    const table = document.getElementById('work-orders-table');
    const rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));

    // Ajouter les badges de statut et formater les technologies (affichage)
    rows.forEach(row => {
        // Formater la technologie (déjà fait côté PHP, mais on garde pour robustesse)
        const techCell = row.cells[2];
        const tech = row.getAttribute('data-technology');
        techCell.textContent = technologyLabels[tech] || tech.toUpperCase();

        // Ajouter le badge de statut
        const statusCell = row.cells[4];
        const status = row.getAttribute('data-status');
        statusCell.innerHTML = `<span class="status-badge status-${status}">${statusLabels[status] || status}</span>`;
    });

    function applyFilters() {
        const selectedTechnology = technologyFilter.value;
        const selectedStatus = statusFilter.value;

        rows.forEach(row => {
            const technology = row.getAttribute('data-technology');
            const status = row.getAttribute('data-status');

            const technologyMatch = !selectedTechnology || technology === selectedTechnology;
            const statusMatch = !selectedStatus || status === selectedStatus;

            row.style.display = technologyMatch && statusMatch ? '' : 'none';
        });
    }

    if (technologyFilter) {
        technologyFilter.addEventListener('change', applyFilters);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }

    // Gestion de la recherche
    const searchInput = document.querySelector('.search-container input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            
            rows.forEach(row => {
                let found = false;
                for (let cell of row.cells) {
                    if (cell.textContent.toLowerCase().includes(searchText)) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? '' : 'none';
            });
        });
    }

    // Ajouter des styles pour les badges de statut
    const style = document.createElement('style');
    style.textContent = `
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            text-transform: capitalize;
        }
        .status-badge.status-1 {
            background-color: #ffd700;
            color: #000;
        }
        .status-badge.status-2 {
            background-color: #3498db;
            color: white;
        }
        .status-badge.status-3 {
            background-color: #2ecc71;
            color: white;
        }
    `;
    document.head.appendChild(style);
}); 