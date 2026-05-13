document.addEventListener('DOMContentLoaded', () => {
    // Recherche dynamique dans le tableau des employés
    const searchInput = document.querySelector('input[placeholder="Rechercher..."]');
    const departmentFilter = document.getElementById('department-filter');

    const filterRows = () => {
        const term = searchInput?.value.toLowerCase() ?? '';
        const department = departmentFilter?.value.toLowerCase() ?? '';
        const rows = document.querySelectorAll('.tbl tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const rowDepartment = (row.dataset.departement || '').toLowerCase();
            const matchesSearch = text.includes(term);
            const matchesDepartment = !department || rowDepartment === department;

            row.style.display = matchesSearch && matchesDepartment ? '' : 'none';
        });
    };

    if (searchInput) {
        searchInput.addEventListener('keyup', filterRows);
    }

    if (departmentFilter) {
        departmentFilter.addEventListener('change', filterRows);
    }

    // Confirmation avant désactivation
    const deleteBtns = document.querySelectorAll('.btn-del');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Voulez-vous vraiment désactiver cet employé ?')) {
                e.preventDefault();
            }
        });
    });
});
