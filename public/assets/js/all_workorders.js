document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-container input');
    const table = document.getElementById('all-work-orders-table');
    if (!searchInput || !table) return;
    const rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));

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
}); 